<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    private static float $weight = 0.2;

    private static array $departments = [
        '総務部',
        '人事部',
        '経理部',
        '営業部',
        'マーケティング部',
        '開発部',
        '情報システム部',
        '生産管理部',
        '品質管理部',
        'カスタマーサポート部',
    ];

    private static array $occupations = [
        '営業職',
        'エンジニア',
        'デザイナー',
        'マーケター',
        '経理担当者',
        '人事担当者',
        'カスタマーサポート',
        'プロジェクトマネージャー',
        'データアナリスト',
        'ライター',
    ];

    private static array $positions = [
        '社長',
        '取締役',
        '部長',
        '課長',
        '係長',
        'リーダー',
        '主任',
        'マネージャー',
        'チーフエンジニア',
        'アシスタント',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_info_id'   => fake()->randomNumber(),
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
            'department'        => fake()->optional(self::$weight)->randomElement(self::$departments),
            'occupation'        => fake()->optional(self::$weight)->randomElement(self::$occupations),
            'position'          => fake()->optional(self::$weight)->randomElement(self::$positions),
            'join_date'         => fake()->optional(self::$weight)->dateTimeBetween('-40 years', 'now')?->format('Y年m月'),
            'introduction'      => fake()->optional(self::$weight)->realText(300),
            'is_master'         => fake()->randomElement([0, 1]),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function master_account(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_master' => 1,
        ]);
    }

    public function sub_account(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_master' => 0,
        ]);
    }
}
