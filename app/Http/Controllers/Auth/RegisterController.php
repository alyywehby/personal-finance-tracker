<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller {
    public function showRegistrationForm() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'locale' => 'en',
        ]);

        // Seed default categories for new user
        $this->seedDefaultCategories($user);

        event(new Registered($user));
        Auth::login($user);

        return redirect('/dashboard');
    }

    private function seedDefaultCategories(User $user): void {
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

        foreach ($categories as $cat) {
            Category::create(array_merge($cat, ['user_id' => $user->id]));
        }
    }
}
