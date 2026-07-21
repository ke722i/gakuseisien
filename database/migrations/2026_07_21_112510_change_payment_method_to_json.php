<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->text('payment_method')->change();
        });

        Schema::table('shop_requests', function (Blueprint $table) {
            $table->text('payment_method')->change();
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('payment_method')->change();
        });

        Schema::table('shop_requests', function (Blueprint $table) {
            $table->string('payment_method')->change();
        });
    }
};