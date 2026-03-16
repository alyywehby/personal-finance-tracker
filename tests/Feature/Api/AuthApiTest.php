<?php
namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'data' => ['token', 'user'], 'message'])
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_registration_fails_with_missing_fields(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['token', 'user']])
            ->assertJson(['success' => true]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct')]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ]);

        $response->assertStatus(422);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = $this->actingAsUser();

        $response = $this->postJson('/api/v1/auth/logout');
        $response->assertOk()->assertJson(['success' => true]);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->getJson('/api/v1/transactions');
        $response->assertStatus(401);
    }
}
