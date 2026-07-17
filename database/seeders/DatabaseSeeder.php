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

        // 先生アカウント（ID: Teacher / PW: 123456）
        User::firstOrCreate(
            ['login_id' => 'Teacher'],
            ['password' => Hash::make('123456'), 'role' => 'teacher'],
        );

        // ｛生徒用｝dummyアカウント
        User::firstOrCreate(
            ['login_id' => 'nishikawa'],
            ['password' => Hash::make('nishikawa1'), 'role' => 'student', 'student_number' => '234054', 'class_number' => 'R4SA24', 'student_name' => '西川', 'homeroom_teacher' => '片山先生']
        );

        User::firstOrCreate(
            ['login_id' => 'kimura'],
            ['password' => Hash::make('kimura1'), 'role' => 'student', 'student_number' => '234043', 'class_number' => 'R4SA09', 'student_name' => '木村', 'homeroom_teacher' => '片山先生']
        );

        User::firstOrCreate(
            ['login_id' => 'mise'],
            ['password' => Hash::make('mise1'), 'role' => 'student', 'student_number' => '234001', 'class_number' => 'R1SA01', 'student_name' => '美勢', 'homeroom_teacher' => '江口先生']
        );

        User::firstOrCreate(
            ['login_id' => 'miyata'],
            ['password' => Hash::make('miyata1'), 'role' => 'student', 'student_number' => '234002', 'class_number' => 'R2SC01', 'student_name' => '宮田', 'homeroom_teacher' => '久徳先生']
        );

        User::firstOrCreate(
            ['login_id' => 'hujita'],
            ['password' => Hash::make('hujita1'), 'role' => 'student', 'student_number' => '234003', 'class_number' => 'R3SC01', 'student_name' => '藤田', 'homeroom_teacher' => '古川先生']
        );
        
        // ｛教師用｝dummyアカウント
        User::firstOrCreate(
            ['login_id' => 'katayama'],
            ['password' => Hash::make('katayama1'), 'role' => 'teacher', 'teacher_number' => '001', 'class_number' => 'R4SA00', 'teacher_name' => '片山']
        );

        // 近辺店舗マップのサンプル店舗データ
        $this->call(ShopSeeder::class);
    }
}
