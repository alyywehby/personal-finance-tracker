<?php
namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_locale_to_arabic(): void
    {
        $user = $this->actingAsUser();

        $response = $this->putJson('/api/v1/user/locale', ['locale' => 'ar']);

        $response->assertOk()->assertJson(['success' => true, 'data' => ['locale' => 'ar']]);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'locale' => 'ar']);
    }

    public function test_user_can_update_locale_to_english(): void
    {
        $user = $this->actingAsUser();
        $user->update(['locale' => 'ar']);

        $response = $this->putJson('/api/v1/user/locale', ['locale' => 'en']);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'locale' => 'en']);
    }

    public function test_invalid_locale_is_rejected(): void
    {
        $this->actingAsUser();

        $response = $this->putJson('/api/v1/user/locale', ['locale' => 'fr']);
        $response->assertStatus(422);
    }
}
