<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 学生向けのお知らせ（通知）テーブル。
     * 先生の操作（教室予約の承認/拒否、欠席届の受理/差し戻しなど）を
     * 学生本人に伝えるために、操作した瞬間に1行書き込む。
     * read_at が null の行が「未読」。
     */
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 通知の宛先ユーザー
            $table->string('title');                    // 例: 教室予約が承認されました
            $table->text('body')->nullable();           // 例: 101c 2026/07/24 3限
            $table->string('link_url')->nullable();     // タップで飛ぶ先（予約一覧など）
            $table->timestamp('read_at')->nullable();   // 既読日時（null = 未読）
            $table->timestamps();

            $table->index(['user_id', 'read_at']); // 未読件数の集計用
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
