<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * reports テーブルは元々 Q&A の質問通報専用（question_id NOT NULL）だったが、
     * 掲示板の投稿通報(ForumController::reportPost)が post_id / type を書き込むため
     * 500 になっていた。両方の通報を同じテーブルで扱えるよう汎用化する。
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (! Schema::hasColumn('reports', 'post_id')) {
                // 掲示板投稿への通報（Q&A通報の場合は null）
                $table->foreignId('post_id')->nullable()->after('question_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('reports', 'type')) {
                // 通報対象の種別（'forum_post' / 'question' など）
                $table->string('type')->nullable()->after('post_id');
            }
        });

        // Q&A以外の通報では question_id を使わないため nullable にする
        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('question_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'post_id')) {
                $table->dropConstrainedForeignId('post_id');
            }
            if (Schema::hasColumn('reports', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
