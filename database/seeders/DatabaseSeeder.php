<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * 何度実行しても同じ状態になるよう、各データは firstOrCreate / updateOrCreate で投入している。
     */
    public function run(): void
    {
        // 動作確認用アカウント（学生・教職員）
        $this->call(UserSeeder::class);

        // 掲示板のサンプル投稿
        $this->seedForumPosts();

        // 空き教室予約：フロアマップ用の部屋データ
        $this->call(RoomSeeder::class);
    }

    /** 掲示板のサンプル投稿（タイトルで重複を防ぐ） */
    private function seedForumPosts(): void
    {
        $posts = [
            [
                'title' => '【新入生歓迎！】テニスサークル メンバー募集',
                'content' => 'テニスが好きな新入生を歓迎します。週2回の活動です。',
                'category' => 'サークル',
                'location' => '体育館前',
                'posted_by' => '体育会テニス部',
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => '黒い財布を見つけました。',
                'content' => '4階の教室で黒い財布を見つけました。心当たりのある方は連絡ください。',
                'category' => '落とし物',
                'location' => '304教室',
                'posted_by' => '学生A',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'マクロ経済学の本譲ります。',
                'content' => '必要であれば格安でお譲りします。状態は良好です。',
                'category' => '教科書',
                'location' => '図書館前',
                'posted_by' => '学生B',
                'published_at' => now()->subDays(3),
            ],
        ];

        foreach ($posts as $post) {
            Post::firstOrCreate(
                ['title' => $post['title']],
                $post,
            );
        }
    }
}
