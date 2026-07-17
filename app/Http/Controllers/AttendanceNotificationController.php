<?php

namespace App\Http\Controllers;

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
        $request->validate([
            'student_number'   => 'required|string',
            'submission_date'  => 'required|date',
            'target_date'      => 'required|date',
            'class_number'     => 'required|string',
            'student_name'     => 'required|string',
            'homeroom_teacher' => 'required|string',
            'reason_category'  => 'required|string',
            'subject_teacher_1' => 'nullable|string',
            // 以下は空欄でもOKな項目
            'periods'           => 'nullable|array', 
            'subject_teacher_2' => 'nullable|string',
            'subject_teacher_3' => 'nullable|string',
            'subject_teacher_4' => 'nullable|string',
            'reason_detail'     => 'nullable|string',
        ]);

        // 2. データベースの「attendance_reports」テーブルに書き込む
        DB::table('attendance_reports')->insert([
            'student_number'    => $request->input('student_number', $user?->student_number),
            'submission_date'   => $request->input('submission_date'),
            'target_date'       => $request->input('target_date'),
            'class_number'      => $request->input('class_number', $user?->class_number),
            'student_name'      => $request->input('student_name', $user?->student_name),
            'homeroom_teacher'  => $request->input('homeroom_teacher', $user?->homeroom_teacher),
            
            // チェックボックス（配列）をPostgreSQLのJSON型に適合するようJSON文字列に変換して保存
            'periods'           => json_encode($request->input('periods')), 
            
            'subject_teacher_1' => $request->input('subject_teacher_1'),
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
                'updated_at'      => now(),
            ]);

        return redirect()->route('notification')->with('success', '届出のステータスを更新しました。');
    }
}