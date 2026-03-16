<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'locale' => 'sometimes|in:en,ar',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'locale' => $validated['locale'] ?? 'en',
        ]);

        // Seed default categories
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

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => ['token' => $token, 'user' => new UserResource($user)],
            'message' => 'Registration successful',
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => ['token' => $token, 'user' => new UserResource($user)],
            'message' => 'Login successful',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Logged out successfully',
        ]);
    }
}
