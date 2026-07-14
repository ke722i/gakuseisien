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
        Schema::create('upvotes', function (Blueprint $table) {
            // 💡 定義書通り複合主キーや単体のidなしの構造にするため、Laravel標準のidは外します
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('answer_id')->constrained('answers')->onDelete('cascade'); // 💡 質問ではなく回答に紐付け
            $table->timestamp('created_at')->useCurrent(); // DEFAULT NOW()

            // 同じ人が同じ回答に2回以上高評価できないようにプライマリキー（一意）に設定
            $table->primary(['user_id', 'answer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upvotes');
    }
};
