<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $user = User::firstOrCreate(
            ['email' => 'demo@finance.app'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'locale' => 'en',
            ]
        );

        $categories = [
            ['name' => 'Food', 'color' => '#EF4444', 'icon' => '🍔'],
            ['name' => 'Rent', 'color' => '#3B82F6', 'icon' => '🏠'],
            ['name' => 'Travel', 'color' => '#10B981', 'icon' => '✈️'],
            ['name' => 'Entertainment', 'color' => '#F59E0B', 'icon' => '🎉'],
            ['name' => 'Healthcare', 'color' => '#EC4899', 'icon' => '🏥'],
            ['name' => 'Shopping', 'color' => '#8B5CF6', 'icon' => '🛍️'],
            ['name' => 'Utilities', 'color' => '#06B6D4', 'icon' => '⚡'],
            ['name' => 'Other', 'color' => '#6B7280', 'icon' => '📦'],
        ];

        $createdCategories = [];
        foreach ($categories as $cat) {
            $createdCategories[] = Category::firstOrCreate(
                ['user_id' => $user->id, 'name' => $cat['name']],
                ['color' => $cat['color'], 'icon' => $cat['icon']]
            );
        }

        if (Transaction::where('user_id', $user->id)->count() === 0) {
            for ($i = 0; $i < 80; $i++) {
                $month = rand(0, 5);
                $day = rand(1, 28);
                $date = now()->subMonths($month)->day($day)->format('Y-m-d');
                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $createdCategories[array_rand($createdCategories)]->id,
                    'type' => fake()->randomElement(['income', 'expense', 'expense', 'expense']),
                    'amount' => fake()->randomFloat(2, 5, 3000),
                    'description' => fake()->optional(0.7)->sentence(),
                    'transaction_date' => $date,
                ]);
            }
        }
    }
}
