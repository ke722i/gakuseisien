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
        Schema::create('questions', function (Blueprint $table) {
            $table->id(); // SERIAL PRIMARY KEY

            // 💡 外部キー制約（->constrained()）を外し、シンプルな数値型（integer）に変更します
            // これにより、schools や users テーブルがまだ無くてもエラーにならずテーブルが作れます！
            $table->integer('school_id')->default(1);
            $table->integer('user_id')->default(1);

            $table->string('title', 255);
            $table->text('content');
            $table->integer('best_answer_id')->nullable();
            $table->string('category', 50);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
