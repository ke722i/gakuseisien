<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 店舗のお気に入り登録（ユーザーと店舗の中間テーブル）。
     * 同じ店舗を二重登録しないよう (user_id, shop_id) を一意にする。
     */
    public function up(): void
    {
        Schema::create('shop_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'shop_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_favorites');
    }
};
