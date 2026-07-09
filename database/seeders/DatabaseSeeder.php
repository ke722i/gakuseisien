<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 動作確認用の学生アカウント（ID: student01 / PW: student1）
        User::firstOrCreate(
            ['login_id' => 'student01'],
            ['password' => Hash::make('student1'), 'role' => 'student'],
        );

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Post::create([
            'title' => '【新入生歓迎！】テニスサークル メンバー募集',
            'content' => 'テニスが好きな新入生を歓迎します。週2回の活動です。',
            'category' => 'サークル',
            'location' => '体育館前',
            'image_url' => 'https://via.placeholder.com/150',
            'posted_by' => '体育会テニス部',
            'published_at' => now()->subDays(1),
        ]);

        Post::create([
            'title' => '黒い財布を見つけました。',
            'content' => '4階の教室で黒い財布を見つけました。心当たりのある方は連絡ください。',
            'category' => '落とし物',
            'location' => '304教室',
            'image_url' => 'https://via.placeholder.com/150',
            'posted_by' => '学生A',
            'published_at' => now()->subDays(2),
        ]);

        Post::create([
            'title' => 'マクロ経済学の本譲ります。',
            'content' => '必要であれば格安でお譲りします。状態は良好です。',
            'category' => '教科書',
            'location' => '図書館前',
            'image_url' => 'https://via.placeholder.com/150',
            'posted_by' => '学生B',
            'published_at' => now()->subDays(3),
        ]);
        // 動作確認用の先生アカウント（ID: teacher01 / PW: teacher1）
        User::firstOrCreate(
            ['login_id' => 'teacher01'],
            ['password' => Hash::make('teacher1'), 'role' => 'teacher'],
        );
    }
}
