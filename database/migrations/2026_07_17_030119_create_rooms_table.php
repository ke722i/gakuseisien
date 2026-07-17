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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('floor');           // 階数（1〜6）
            $table->string('room_code');                    // 教室番号・施設名（例: 101c, 会議室1）
            $table->string('name');                          // 表示名（room_codeと同じでよい）
            $table->string('room_type')->default('classroom'); // classroom / meeting / office / utility
            $table->boolean('is_reservable')->default(true); // 予約可能かどうか（職員室・EV・階段などはfalse）
            $table->unsignedSmallInteger('display_order')->default(0); // フロアマップ内の表示順
            $table->timestamps();

            $table->unique(['floor', 'room_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
