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
        Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('shop_id')->constrained()->onDelete('cascade');

    $table->integer('rating');
    $table->text('comment');

    // 投稿者本人を識別するためのトークン
    $table->string('edit_token', 64);

    $table->boolean('is_visible')->default(true);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
