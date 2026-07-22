<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 教職員の担当科目を保持する列を追加する。
     *
     * 欠席届の「科目教師」プルダウンが「科目教師1」などのダミー文字列のままで、
     * 実在の教員を選べなかったため、教員アカウントの担当科目をここに持たせて
     * 「氏名（担当科目）」の形で選択できるようにする。
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('teacher_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('subject');
        });
    }
};
