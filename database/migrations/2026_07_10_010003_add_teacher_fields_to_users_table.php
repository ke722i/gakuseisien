<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    // 学籍番号やクラス番号を追加したVerの教師usersテーブルを作成するマイグレーションファイル
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'teacher_number')) {
                $table->string('teacher_number')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'class_number')) {
                $table->string('class_number')->nullable()->after('teacher_number');
            }
            if (!Schema::hasColumn('users', 'teacher_name')) {
                $table->string('teacher_name')->nullable()->after('class_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['teacher_number', 'class_number', 'teacher_name']);
        });
    }
};
