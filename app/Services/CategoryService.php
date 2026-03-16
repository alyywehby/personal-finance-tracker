<?php
namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function getForUser(int $userId): Collection
    {
        return Category::where('user_id', $userId)
            ->withCount('transactions')
            ->get();
    }

    public function create(User $user, array $data): Category
    {
        return $user->categories()->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }

    public function delete(Category $category): bool
    {
        if ($category->transactions()->exists()) {
            return false;
        }

        $category->delete();
        return true;
    }

    public function authorize(Category $category, int $userId): void
    {
        if ($category->user_id !== $userId) {
            abort(403);
        }
    }

    public function seedDefaults(User $user): void
    {
        $categories = [
            ['name' => 'Food',          'color' => '#EF4444', 'icon' => '🍔'],
            ['name' => 'Rent',          'color' => '#3B82F6', 'icon' => '🏠'],
            ['name' => 'Travel',        'color' => '#10B981', 'icon' => '✈️'],
            ['name' => 'Entertainment', 'color' => '#F59E0B', 'icon' => '🎉'],
            ['name' => 'Healthcare',    'color' => '#EC4899', 'icon' => '🏥'],
            ['name' => 'Shopping',      'color' => '#8B5CF6', 'icon' => '🛍️'],
            ['name' => 'Utilities',     'color' => '#06B6D4', 'icon' => '⚡'],
            ['name' => 'Other',         'color' => '#6B7280', 'icon' => '📦'],
        ];

        foreach ($categories as $cat) {
            Category::create(array_merge($cat, ['user_id' => $user->id]));
        }
    }
}
