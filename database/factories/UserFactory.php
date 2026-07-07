<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 英数字6〜10文字のランダムなログインID
            'login_id' => fake()->unique()->bothify('user###'),
            'password' => static::$password ??= Hash::make('password1'),
            'role' => 'student',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * 先生アカウントの状態を指定する。
     */
    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'teacher',
        ]);
    }
}
