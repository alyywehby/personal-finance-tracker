<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(private CategoryService $categoryService) {}

    public function register(array $data, string $locale = 'en'): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'locale'   => $locale,
        ]);

        $this->categoryService->seedDefaults($user);

        return $user;
    }
}
