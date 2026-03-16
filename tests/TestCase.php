<?php
namespace Tests;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    protected User $user;
    protected array $categories = [];

    protected function createUserWithCategories(): User
    {
        $user = User::factory()->create(['locale' => 'en']);
        $defaultCategories = [
            ['name' => 'Food', 'color' => '#EF4444', 'icon' => '🍔'],
            ['name' => 'Entertainment', 'color' => '#F59E0B', 'icon' => '🎉'],
            ['name' => 'Other', 'color' => '#6B7280', 'icon' => '📦'],
        ];
        foreach ($defaultCategories as $cat) {
            $this->categories[] = Category::factory()->create(array_merge($cat, ['user_id' => $user->id]));
        }
        return $user;
    }

    protected function actingAsUser(?User $user = null): User
    {
        $user = $user ?? $this->createUserWithCategories();
        Sanctum::actingAs($user);
        return $user;
    }
}
