<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 未使用のスキーマを整理する。
     *  - answers.is_teacher_approved: 実際に使われるのは is_approved 列で、こちらはどこからも読み書きされない。
     *  - upvotes テーブル: いいね機能は answer_user ピボット + upvote_count 列で実装済みで、こちらは未使用。
     */
    public function up(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            if (Schema::hasColumn('answers', 'is_teacher_approved')) {
                $table->dropColumn('is_teacher_approved');
            }
        });

        Schema::dropIfExists('upvotes');
    }

    /**
     * Reverse the migrations.
     *
     * is_teacher_approved のみ復元する。upvotes テーブルの復元は
     * 元の create_upvotes_table マイグレーションに委ねる。
     */
    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            if (! Schema::hasColumn('answers', 'is_teacher_approved')) {
                $table->boolean('is_teacher_approved')->default(false);
            }
        });
    }
};
