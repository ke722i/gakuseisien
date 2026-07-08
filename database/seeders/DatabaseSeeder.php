<?php

namespace Database\Seeders;

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

        // 動作確認用の先生アカウント（ID: teacher01 / PW: teacher1）
        User::firstOrCreate(
            ['login_id' => 'teacher01'],
            ['password' => Hash::make('teacher1'), 'role' => 'teacher'],
        );
    }
}
