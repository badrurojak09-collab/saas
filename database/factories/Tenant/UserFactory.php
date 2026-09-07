<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\UserStatus;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('password'),
            'status' => UserStatus::Active,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'metadata' => [],
        ];
    }
}
