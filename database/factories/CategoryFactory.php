<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory {
    public function definition(): array {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->unique()->word(),
            'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
            'icon' => $this->faker->randomElement(['💰', '🏠', '✈️', '🎉', '🏥', '🛍️', '⚡', '📦', null]),
        ];
    }
}
