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
        // 既存の古いテーブルがあれば一度完全に削除する
        Schema::dropIfExists('answer_user');

        Schema::create('answer_user', function (Blueprint $table) {
            $table->id();

            // 💡 questionsテーブル同様、エラーを防ぐためシンプルな integer 型で定義します
            $table->integer('answer_id');
            $table->integer('user_id');

            $table->timestamps();

            // 同じユーザーが同じ回答に何度も投票できないユニーク制約
            $table->unique(['answer_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answer_user');
    }
};
