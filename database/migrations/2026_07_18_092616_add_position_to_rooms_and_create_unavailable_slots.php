<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     *  - rooms にフロアマップ上の位置（pos_x/pos_y/width/height）を追加し、
     *    Blade にハードコードされていた座標を DB へ移して「教室の登録」を可能にする。
     *  - room_unavailable_slots テーブルを新設し、教室ごとの「利用不可時間帯」を設定できるようにする。
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('pos_x')->default(0);
            $table->integer('pos_y')->default(0);
            $table->integer('width')->default(120);
            $table->integer('height')->default(100);
        });

        // これまで Blade に埋め込まれていた各階の座標を DB へ移行する
        foreach ($this->roomShapesByFloor() as $floor => $shapes) {
            foreach ($shapes as $code => $s) {
                DB::table('rooms')
                    ->where('floor', $floor)
                    ->where('room_code', $code)
                    ->update([
                        'pos_x' => $s['x'],
                        'pos_y' => $s['y'],
                        'width' => $s['w'],
                        'height' => $s['h'],
                    ]);
            }
        }

        Schema::create('room_unavailable_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->date('date');          // 利用不可の日付
            $table->unsignedTinyInteger('period'); // 時限（1〜6）
            $table->string('reason')->nullable();  // 理由（メンテナンス等）
            $table->timestamps();

            $table->unique(['room_id', 'date', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_unavailable_slots');

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['pos_x', 'pos_y', 'width', 'height']);
        });
    }

    /** 旧 Blade の $roomShapesByFloor をそのまま移設したもの */
    private function roomShapesByFloor(): array
    {
        return [
            1 => [
                '101c' => ['x' => 100, 'y' => 40, 'w' => 260, 'h' => 330],
                '会議室1' => ['x' => 460, 'y' => 40, 'w' => 180, 'h' => 140],
                '会議室2' => ['x' => 650, 'y' => 40, 'w' => 180, 'h' => 140],
                '職員室' => ['x' => 460, 'y' => 220, 'w' => 280, 'h' => 230],
                '物置' => ['x' => 100, 'y' => 390, 'w' => 120, 'h' => 110],
                'EV' => ['x' => 230, 'y' => 390, 'w' => 90, 'h' => 110],
                '階段' => ['x' => 330, 'y' => 390, 'w' => 110, 'h' => 110],
            ],
            2 => [
                '202c' => ['x' => 100, 'y' => 40, 'w' => 250, 'h' => 115],
                '203c' => ['x' => 100, 'y' => 155, 'w' => 250, 'h' => 115],
                '理事長室' => ['x' => 100, 'y' => 270, 'w' => 250, 'h' => 140],
                '201c' => ['x' => 390, 'y' => 40, 'w' => 220, 'h' => 250],
                '事務室' => ['x' => 630, 'y' => 40, 'w' => 170, 'h' => 140],
                '保健室' => ['x' => 630, 'y' => 180, 'w' => 110, 'h' => 45],
                'EV' => ['x' => 355, 'y' => 440, 'w' => 60, 'h' => 60],
                '階段' => ['x' => 420, 'y' => 380, 'w' => 80, 'h' => 120],
                'PS' => ['x' => 505, 'y' => 380, 'w' => 42, 'h' => 120],
                '女子トイレ' => ['x' => 552, 'y' => 380, 'w' => 110, 'h' => 120],
            ],
            3 => [
                '301' => ['x' => 100, 'y' => 40, 'w' => 235, 'h' => 130],
                '302' => ['x' => 335, 'y' => 40, 'w' => 235, 'h' => 130],
                '303' => ['x' => 570, 'y' => 40, 'w' => 185, 'h' => 175],
                '304c' => ['x' => 140, 'y' => 220, 'w' => 200, 'h' => 240],
                '305' => ['x' => 345, 'y' => 220, 'w' => 160, 'h' => 95],
                'EV' => ['x' => 355, 'y' => 440, 'w' => 60, 'h' => 60],
                '階段' => ['x' => 420, 'y' => 380, 'w' => 80, 'h' => 120],
                'PS' => ['x' => 505, 'y' => 380, 'w' => 42, 'h' => 120],
                '男子トイレ' => ['x' => 552, 'y' => 380, 'w' => 110, 'h' => 120],
            ],
            4 => [
                '403c' => ['x' => 110, 'y' => 40, 'w' => 235, 'h' => 250],
                '402c' => ['x' => 345, 'y' => 40, 'w' => 225, 'h' => 220],
                '401c' => ['x' => 570, 'y' => 40, 'w' => 185, 'h' => 185],
                'EV' => ['x' => 355, 'y' => 440, 'w' => 60, 'h' => 60],
                '階段' => ['x' => 420, 'y' => 380, 'w' => 80, 'h' => 120],
                'PS' => ['x' => 505, 'y' => 380, 'w' => 42, 'h' => 120],
                '男子トイレ' => ['x' => 552, 'y' => 380, 'w' => 58, 'h' => 120],
                '女子トイレ' => ['x' => 615, 'y' => 380, 'w' => 58, 'h' => 120],
            ],
            5 => [
                '501' => ['x' => 100, 'y' => 40, 'w' => 235, 'h' => 130],
                '502' => ['x' => 335, 'y' => 40, 'w' => 235, 'h' => 130],
                '503' => ['x' => 570, 'y' => 40, 'w' => 185, 'h' => 175],
                '504c' => ['x' => 140, 'y' => 220, 'w' => 200, 'h' => 240],
                '505' => ['x' => 345, 'y' => 220, 'w' => 160, 'h' => 95],
                'EV' => ['x' => 355, 'y' => 440, 'w' => 60, 'h' => 60],
                '階段' => ['x' => 420, 'y' => 380, 'w' => 80, 'h' => 120],
                'PS' => ['x' => 505, 'y' => 380, 'w' => 42, 'h' => 120],
                '女子トイレ' => ['x' => 552, 'y' => 380, 'w' => 110, 'h' => 120],
            ],
            6 => [
                '601' => ['x' => 100, 'y' => 40, 'w' => 235, 'h' => 130],
                '602' => ['x' => 335, 'y' => 40, 'w' => 235, 'h' => 130],
                '603' => ['x' => 570, 'y' => 40, 'w' => 185, 'h' => 175],
                '604c' => ['x' => 140, 'y' => 220, 'w' => 200, 'h' => 240],
                '605' => ['x' => 345, 'y' => 220, 'w' => 160, 'h' => 95],
                'EV' => ['x' => 355, 'y' => 440, 'w' => 60, 'h' => 60],
                '階段' => ['x' => 420, 'y' => 380, 'w' => 80, 'h' => 120],
                'PS' => ['x' => 505, 'y' => 380, 'w' => 42, 'h' => 120],
                '男子トイレ' => ['x' => 552, 'y' => 380, 'w' => 110, 'h' => 120],
            ],
        ];
    }
};
