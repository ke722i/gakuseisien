<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // 直接データベースを操作するクラスをインポート

class AttendanceNotificationController extends Controller
{
    /**
     * 【生徒用】欠席・遅刻届の新規提出（生徒はattendance_typeを選ばない）
     */
    public function storeNotification(Request $request)
    {
        $user = Auth::user();

        // 1. 入力データのチェック（バリデーション）
        //    学籍番号・クラス・氏名・担任はアカウント情報を使うため、画面からは受け取らない
        $request->validate([
            // 提出日は画面の値を信用せずサーバー側の日付を使うため、ここでは検証しない。
            // 欠席日は、後から出す届（昨日休んだ等）も認めるため過去日を許可する。
            // ただし極端な未来日は誤入力とみなして弾く（1年先まで）。
            'target_date'      => 'required|date|before_or_equal:' . now()->addYear()->toDateString(),
            'reason_category'  => 'required|string',
            // DB側が NOT NULL のため必須（画面のプルダウンは常に値を送る）
            'subject_teacher_1' => 'required|string',
            // 以下は空欄でもOKな項目
            'periods'           => 'nullable|array',
            'subject_teacher_2' => 'nullable|string',
            'subject_teacher_3' => 'nullable|string',
            'subject_teacher_4' => 'nullable|string',
            'reason_detail'     => 'nullable|string',
        ]);

        // 学籍番号やクラスが未登録だと担任に届かないため、先に知らせる
        if (! $user?->student_number || ! $user?->class_number) {
            return redirect()->back()
                ->with('error', '学籍番号またはクラス番号が未登録のため提出できません。担任の先生にアカウント情報の登録を依頼してください。');
        }

        // 2. データベースの「attendance_reports」テーブルに書き込む
        //    本人になりすまして提出できないよう、身元にあたる項目はログイン中のアカウントから取る
        DB::table('attendance_reports')->insert([
            'student_number'    => $user->student_number,
            // 提出日は改ざんできないようサーバー側の日付で確定させる
            'submission_date'   => now()->toDateString(),
            'target_date'       => $request->input('target_date'),
            'class_number'      => $user->class_number,
            'student_name'      => $user->student_name ?: $user->login_id,
            'homeroom_teacher'  => $user->homeroom_teacher,

            // チェックボックス（配列）をPostgreSQLのJSON型に適合するようJSON文字列に変換して保存
            'periods'           => json_encode($request->input('periods')), 
            
            'subject_teacher_1' => $request->input('subject_teacher_1', ''),
            'subject_teacher_2' => $request->input('subject_teacher_2'),
            'subject_teacher_3' => $request->input('subject_teacher_3'),
            'subject_teacher_4' => $request->input('subject_teacher_4'),
            'reason_category'   => $request->input('reason_category'),
            'reason_detail'     => $request->input('reason_detail'),
            
            'attendance_type'   => null, // 生徒は提出時に出席状況を選ばないため、nullで初期化
            'report_status'     => '未処理',// 初期状態は「未処理」とする

            // 現在時刻を自動挿入
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // 3. 保存した後は、完了画面や元の画面にリダイレクトする
        return redirect()->back()->with('success', '欠席・遅刻届を提出しました。');
    }

    /**
    * 【教師用】届出を確認し、attendance_type（欠席・遅刻など）を決定する
    */
    public function decideNotificationType(Request $request, $id)
    {
        // 1. report_status（受理 or 差し戻し）のバリデーション
        $rules = [
            'report_status' => 'required|string|in:受理,差し戻し',
            'return_comment' => 'nullable|string|max:1000', // 差し戻し理由コメント
        ];

        // 2. 「受理」の場合のみ、attendance_type（ラジオボタン）を必須にする
        if ($request->input('report_status') === '受理') {
            $rules['attendance_type'] = 'required|string|in:病気,欠席,遅刻,その他';
        } else {
            $rules['attendance_type'] = 'nullable|string|in:病気,欠席,遅刻,その他';
        }

        $request->validate($rules);

        // 3. 対象の届出データを更新
        DB::table('attendance_reports')
            ->where('id', $id)
            ->update([
                'attendance_type' => $request->input('attendance_type'),
                'report_status'   => $request->input('report_status'), // 追加：ステータスを更新
                'return_comment'  => $request->input('return_comment'), // 差し戻しコメントを保存
                'updated_at'      => now(),
            ]);

        // 4. 提出した学生に結果を通知する
        //    届は学籍番号しか持たないため、学籍番号からユーザーを逆引きする
        //    （見つからない場合は通知なしで続行）
        $report = DB::table('attendance_reports')->find($id);
        $student = User::where('student_number', $report->student_number)->first();

        $isAccepted = $request->input('report_status') === '受理';
        $body = $report->target_date . ' 分の届';
        if (! $isAccepted && $request->filled('return_comment')) {
            $body .= '／コメント: ' . $request->input('return_comment');
        }

        UserNotification::send(
            $student?->id,
            $isAccepted ? '欠席・遅刻届が受理されました' : '欠席・遅刻届が差し戻されました',
            $body,
            route('notification', absolute: false)
        );

        // どの操作が完了したのかが分かる文言にする
        $studentLabel = $report->student_name ?: $report->student_number;
        $message = $isAccepted
            ? "{$studentLabel} さんの届出を受理しました。本人に通知を送りました。"
            : "{$studentLabel} さんの届出を差し戻しました。本人に通知を送りました。";

        return redirect()->route('notification')->with('success', $message);
    }
}