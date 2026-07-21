<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

/**
 * 各階のフロアマップ（Figmaの教室図のレイアウト）。
 *
 * ■ 予約可否
 *   教室（数字・c付き）と会議室は予約可能、事務室・保健室・理事長室・トイレ・EV・階段・PS・物置は予約対象外。
 *   ※ 末尾「c」はコンピュータールーム（PC設置教室）を表す識別子。表記ゆれではないので残す。
 *
 * ■ 位置情報（pos_x/pos_y/width/height）について
 *   フロアマップはこの座標を使ってSVGを描画する。
 *   以前は座標を「マイグレーション内のUPDATE」で流し込んでいたため、
 *   まだ rooms が空の状態で migrate した環境（新規に clone したメンバー）では
 *   UPDATE が空振りし、全教室が座標(0,0)＝左上に重なって「教室が1つ」に見えていた。
 *   そこで座標もこのSeederで設定し、updateOrCreate により
 *   「migrate → db:seed」だけでどの環境でも正しいマップになるようにしている。
 *   既存環境で再実行しても座標が最新に更新されるだけで重複は生じない。
 */
class RoomSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rooms() as $floor => $rooms) {
            foreach ($rooms as $order => $room) {
                [$code, $type, $reservable, $x, $y, $w, $h] = $room;

                Room::updateOrCreate(
                    ['floor' => $floor, 'room_code' => $code],
                    [
                        'name' => $code,
                        'room_type' => $type,
                        'is_reservable' => $reservable,
                        'display_order' => $order + 1,
                        'pos_x' => $x,
                        'pos_y' => $y,
                        'width' => $w,
                        'height' => $h,
                    ],
                );
            }
        }
    }

    /**
     * 階ごとの教室定義。
     * 各行: [room_code, room_type, is_reservable, pos_x, pos_y, width, height]
     */
    private function rooms(): array
    {
        return [
            1 => [
                ['101c', 'classroom', true, 100, 40, 260, 330],
                ['会議室1', 'meeting', true, 460, 40, 180, 140],
                ['会議室2', 'meeting', true, 650, 40, 180, 140],
                ['職員室', 'office', false, 460, 220, 280, 230],
                ['物置', 'utility', false, 100, 390, 120, 110],
                ['EV', 'utility', false, 230, 390, 90, 110],
                ['階段', 'utility', false, 330, 390, 110, 110],
            ],
            2 => [
                ['202c', 'classroom', true, 100, 40, 250, 115],
                ['203c', 'classroom', true, 100, 155, 250, 115],
                ['201c', 'classroom', true, 390, 40, 220, 250],
                ['事務室', 'office', false, 630, 40, 170, 140],
                ['保健室', 'office', false, 630, 180, 110, 45],
                ['理事長室', 'office', false, 100, 270, 250, 140],
                ['EV', 'utility', false, 355, 440, 60, 60],
                ['階段', 'utility', false, 420, 380, 80, 120],
                ['PS', 'utility', false, 505, 380, 42, 120],
                ['女子トイレ', 'utility', false, 552, 380, 110, 120],
            ],
            3 => [
                ['301', 'classroom', true, 100, 40, 235, 130],
                ['302', 'classroom', true, 335, 40, 235, 130],
                ['303', 'classroom', true, 570, 40, 185, 175],
                ['304c', 'classroom', true, 140, 220, 200, 240],
                ['305', 'classroom', true, 345, 220, 160, 95],
                ['EV', 'utility', false, 355, 440, 60, 60],
                ['階段', 'utility', false, 420, 380, 80, 120],
                ['PS', 'utility', false, 505, 380, 42, 120],
                ['男子トイレ', 'utility', false, 552, 380, 110, 120],
            ],
            4 => [
                ['403c', 'classroom', true, 110, 40, 235, 250],
                ['402c', 'classroom', true, 345, 40, 225, 220],
                ['401c', 'classroom', true, 570, 40, 185, 185],
                ['EV', 'utility', false, 355, 440, 60, 60],
                ['階段', 'utility', false, 420, 380, 80, 120],
                ['PS', 'utility', false, 505, 380, 42, 120],
                ['男子トイレ', 'utility', false, 552, 380, 58, 120],
                ['女子トイレ', 'utility', false, 615, 380, 58, 120],
            ],
            5 => [
                ['501', 'classroom', true, 100, 40, 235, 130],
                ['502', 'classroom', true, 335, 40, 235, 130],
                ['503', 'classroom', true, 570, 40, 185, 175],
                ['504c', 'classroom', true, 140, 220, 200, 240],
                ['505', 'classroom', true, 345, 220, 160, 95],
                ['EV', 'utility', false, 355, 440, 60, 60],
                ['階段', 'utility', false, 420, 380, 80, 120],
                ['PS', 'utility', false, 505, 380, 42, 120],
                ['女子トイレ', 'utility', false, 552, 380, 110, 120],
            ],
            6 => [
                ['601', 'classroom', true, 100, 40, 235, 130],
                ['602', 'classroom', true, 335, 40, 235, 130],
                ['603', 'classroom', true, 570, 40, 185, 175],
                ['604c', 'classroom', true, 140, 220, 200, 240],
                ['605', 'classroom', true, 345, 220, 160, 95],
                ['EV', 'utility', false, 355, 440, 60, 60],
                ['階段', 'utility', false, 420, 380, 80, 120],
                ['PS', 'utility', false, 505, 380, 42, 120],
                ['男子トイレ', 'utility', false, 552, 380, 110, 120],
            ],
        ];
    }
}
