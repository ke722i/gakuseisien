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
        Schema::create('shops', function (Blueprint $table) {
    $table->id();

    // 店舗情報
    $table->string('name');                 // 店舗名
    $table->string('genre');                // ジャンル
    $table->string('address');              // 住所
    $table->string('business_hours');       // 営業時間
    $table->integer('budget');              // 予算
    $table->integer('distance');            // 学校からの距離(m)
    $table->string('payment_method');       // 決済方法
    $table->boolean('is_visible')->default(true); // 表示・非表示

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
