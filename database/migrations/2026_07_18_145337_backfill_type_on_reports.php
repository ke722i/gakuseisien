<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 通報管理一覧で掲示板とQ&Aを区別できるよう、type が未設定の既存データを埋める。
     * （Q&Aの通報は以前 type を保存していなかったため）
     */
    public function up(): void
    {
        DB::table('reports')
            ->whereNull('type')
            ->whereNotNull('question_id')
            ->update(['type' => 'question']);

        DB::table('reports')
            ->whereNull('type')
            ->whereNotNull('post_id')
            ->update(['type' => 'forum_post']);
    }

    /**
     * Reverse the migrations.
     *
     * データの補完のみのため、巻き戻しでは何もしない。
     */
    public function down(): void
    {
        //
    }
};
