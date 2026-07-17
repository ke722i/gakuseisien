<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * 近辺店舗マップのサンプルデータ。
     * store/home.blade.php にハードコードされていたモック店舗をDBへ移したもの。
     * firstOrCreate なので何度実行しても重複しない。
     */
    public function run(): void
    {
        $shops = [
            [
                'name' => 'ラーメン〇〇',
                'genre' => 'ラーメン',
                'address' => '大阪市〇〇区〇〇1-2-3',
                'business_hours' => '11:00～22:00',
                'budget' => 800,
                'distance' => 240, // 徒歩3分相当
                'payment_method' => '現金・PayPay',
            ],
            [
                'name' => 'カフェ△△',
                'genre' => 'カフェ',
                'address' => '大阪市〇〇区〇〇2-3-4',
                'business_hours' => '9:00～18:00',
                'budget' => 700,
                'distance' => 400, // 徒歩5分相当
                'payment_method' => '現金・クレジット',
            ],
            [
                'name' => 'カレー□□',
                'genre' => '定食',
                'address' => '大阪市〇〇区〇〇3-4-5',
                'business_hours' => '11:00～21:00',
                'budget' => 900,
                'distance' => 320,
                'payment_method' => '現金・電子マネー',
            ],
            [
                'name' => '定食屋◇◇',
                'genre' => '定食',
                'address' => '大阪市〇〇区〇〇4-5-6',
                'business_hours' => '10:30～20:00',
                'budget' => 750,
                'distance' => 480,
                'payment_method' => '現金',
            ],
            [
                'name' => '寿司××',
                'genre' => '寿司',
                'address' => '大阪市〇〇区〇〇5-6-7',
                'business_hours' => '11:00～22:30',
                'budget' => 1200,
                'distance' => 560,
                'payment_method' => '現金・クレジット・PayPay',
            ],
            [
                'name' => 'コンビニ☆☆',
                'genre' => 'コンビニ',
                'address' => '大阪市〇〇区〇〇6-7-8',
                'business_hours' => '24時間営業',
                'budget' => 500,
                'distance' => 160,
                'payment_method' => '現金・クレジット・PayPay・電子マネー',
            ],
        ];

        foreach ($shops as $shop) {
            Shop::firstOrCreate(
                ['name' => $shop['name']],
                $shop + ['is_visible' => true],
            );
        }
    }
}
