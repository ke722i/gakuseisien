<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_reports', function (Blueprint $table) {
            $table->id();
            
            // 画面項目
            $table->string('student_number');                    // 学籍番号
            $table->date('submission_date');                     // 提出日 (2026-07-10)
            $table->date('target_date');                         // 日付 (2026/07/10)
            
            // 他テーブルから引っ張ってくる予定の項目
            $table->string('class_number');                      // クラス番号 (外部参照用)
            $table->string('student_name');                      // 名前 (外部参照用)
            $table->string('homeroom_teacher');                  // 担任教師 (外部参照用)
            
            // 時限 (チェックボックス複数選択：JSON形式で [1, 2] や [3] などの形で保存を想定)
            $table->json('periods')->nullable();                 // 時限 (1, 2, 3, 4)
            
            // 科目教師 (1〜4つ入る可能性があるため、4つの枠を用意)
            $table->string('subject_teacher_1');                 // 科目教師1
            $table->string('subject_teacher_2')->nullable();     // 科目教師2
            $table->string('subject_teacher_3')->nullable();     // 科目教師3
            $table->string('subject_teacher_4')->nullable();     // 科目教師4
            
            // 理由 (プルダウンと自由入力テキストの2つが同時に存在)
            $table->string('reason_category');                   // 理由（プルダウン選択値：体調不良など）
            $table->text('reason_detail');           // 理由（テキスト入力された詳細）

            $table->timestamps();          // 作成日時・更新日時

            $table->text('return_comment')->nullable(); // 差し戻しコメント
            $table->string('attendance_type')->nullable(); // 出席状況（遅刻・早退・欠課など）。一旦nullでok
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_reports');
    }
};