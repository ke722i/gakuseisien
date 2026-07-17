<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('answers', function (Blueprint $table) {
            // カラムを追加（デフォルト値は false）
            $table->boolean('is_approved')->default(false);
        });
    }

    public function down()
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};
