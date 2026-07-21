<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * ShopController が読み書きする official_url カラムが shops / shop_requests
     * どちらのテーブルにも存在せず、店舗申請の承認・店舗更新で500になっていたため追加する。
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            if (! Schema::hasColumn('shops', 'official_url')) {
                $table->string('official_url')->nullable()->after('payment_method');
            }
        });

        Schema::table('shop_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('shop_requests', 'official_url')) {
                $table->string('official_url')->nullable()->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            if (Schema::hasColumn('shops', 'official_url')) {
                $table->dropColumn('official_url');
            }
        });

        Schema::table('shop_requests', function (Blueprint $table) {
            if (Schema::hasColumn('shop_requests', 'official_url')) {
                $table->dropColumn('official_url');
            }
        });
    }
};
