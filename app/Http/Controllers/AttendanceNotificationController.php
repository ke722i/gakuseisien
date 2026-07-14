<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // 直接データベースを操作するクラスをインポート

class AttendanceNotificationController extends Controller
{
    /**
     * 【生徒用】欠席・遅刻届の新規提出（生徒はreport_typeを選ばない）
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

            // 現在時刻を自動挿入
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // 3. 保存した後は、完了画面や元の画面にリダイレクトする
        return redirect()->back()->with('success', '欠席・遅刻届を提出しました。');
    }

    /**
    * 【教師用】届出を確認し、report_type（欠席・遅刻など）を決定する
    */
    public function decideNotificationType(Request $request, $id)
    {
        // 教師の処理でのみ「report_type」を必須（required）にする！
        $request->validate([
            'attendance_type' => 'required|string|in:病気,欠席,遅刻,その他', // 指定の文字のみ許可
        ]);

        // 対象の届出データを教師が選んだ種別で更新する
        DB::table('attendance_reports')
            ->where('id', $id)
            ->update([
                'report_type' => $request->input('report_type'),
                'updated_at'  => now(),
            ]);

        return redirect()->route('teacher.dashboard')->with('success', '届出の種別を確定しました。');
    }
    
}