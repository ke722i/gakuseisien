<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * 各階のフロアマップ（添付画像のレイアウト）。
     * 教室（c付き・数字のみ）は予約可能、事務室・保健室・理事長室・トイレ・EV・階段・PSは予約対象外。
     */
    public function run(): void
    {
        $floors = [
            1 => [
                ['room_code' => '101c', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '会議室1', 'room_type' => 'meeting', 'is_reservable' => true],
                ['room_code' => '会議室2', 'room_type' => 'meeting', 'is_reservable' => true],
                ['room_code' => '職員室', 'room_type' => 'office', 'is_reservable' => false],
                ['room_code' => '物置', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => 'EV', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => '階段', 'room_type' => 'utility', 'is_reservable' => false],
            ],
            2 => [
                ['room_code' => '202c', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '203c', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '201c', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '事務室', 'room_type' => 'office', 'is_reservable' => false],
                ['room_code' => '保健室', 'room_type' => 'office', 'is_reservable' => false],
                ['room_code' => '理事長室', 'room_type' => 'office', 'is_reservable' => false],
                ['room_code' => 'EV', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => '階段', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => 'PS', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => '女子トイレ', 'room_type' => 'utility', 'is_reservable' => false],
            ],
            3 => [
                ['room_code' => '301', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '302', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '303', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '304c', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '305', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => 'EV', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => '階段', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => 'PS', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => '男子トイレ', 'room_type' => 'utility', 'is_reservable' => false],
            ],
            4 => [
                ['room_code' => '403c', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '402c', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '401c', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => 'EV', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => '階段', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => 'PS', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => '男子トイレ', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => '女子トイレ', 'room_type' => 'utility', 'is_reservable' => false],
            ],
            5 => [
                ['room_code' => '501', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '502', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '503', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '504c', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '505', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => 'EV', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => '階段', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => 'PS', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => '女子トイレ', 'room_type' => 'utility', 'is_reservable' => false],
            ],
            6 => [
                ['room_code' => '601', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '602', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '603', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '604c', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => '605', 'room_type' => 'classroom', 'is_reservable' => true],
                ['room_code' => 'EV', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => '階段', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => 'PS', 'room_type' => 'utility', 'is_reservable' => false],
                ['room_code' => '男子トイレ', 'room_type' => 'utility', 'is_reservable' => false],
            ],
        ];

        foreach ($floors as $floor => $rooms) {
            foreach ($rooms as $order => $room) {
                Room::firstOrCreate(
                    ['floor' => $floor, 'room_code' => $room['room_code']],
                    $room + [
                        'floor' => $floor,
                        'name' => $room['room_code'],
                        'display_order' => $order + 1,
                    ],
                );
            }
        }
    }
}
