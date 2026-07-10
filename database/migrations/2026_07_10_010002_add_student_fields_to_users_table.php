<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    // 学籍番号やクラス番号を追加したVerの生徒usersテーブルを作成するマイグレーションファイル
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'student_number')) {
                $table->string('student_number')->nullable()->after('role');
            }
            if (! Schema::hasColumn('users', 'class_number')) {
                $table->string('class_number')->nullable()->after('student_number');
            }
            if (! Schema::hasColumn('users', 'student_name')) {
                $table->string('student_name')->nullable()->after('class_number');
            }
            if (! Schema::hasColumn('users', 'homeroom_teacher')) {
                $table->string('homeroom_teacher')->nullable()->after('student_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['student_number', 'class_number', 'student_name', 'homeroom_teacher']);
        });
    }
};
