<?php
namespace Tests\Feature\Api;

use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // 500 — unexpected exception
    // -------------------------------------------------------------------------

    public function test_500_returns_json_with_exception_message_in_debug_mode(): void
    {
        config(['app.debug' => true]);

        $this->mock(TransactionService::class)
            ->shouldReceive('getFilteredQuery')
            ->andThrow(new RuntimeException('Something went wrong internally'));

        $this->actingAsUser();

        $this->getJson('/api/v1/transactions')
            ->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => 'Something went wrong internally',
                'errors'  => [],
            ]);
    }

    public function test_500_hides_exception_message_in_production_mode(): void
    {
        config(['app.debug' => false]);

        $this->mock(TransactionService::class)
            ->shouldReceive('getFilteredQuery')
            ->andThrow(new RuntimeException('Sensitive internal detail'));

        $this->actingAsUser();

        $this->getJson('/api/v1/transactions')
            ->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => 'Server error',
                'errors'  => [],
            ])
            ->assertJsonMissing(['message' => 'Sensitive internal detail']);
    }

    // -------------------------------------------------------------------------
    // 422 — validation failure
    // -------------------------------------------------------------------------

    public function test_422_returns_validation_errors(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/v1/transactions', [])
            ->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [],
            ])
            ->assertJson(['success' => false, 'message' => 'Validation failed']);
    }

    // -------------------------------------------------------------------------
    // 401 — unauthenticated
    // -------------------------------------------------------------------------

    public function test_401_returns_json_when_unauthenticated(): void
    {
        $this->getJson('/api/v1/transactions')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated',
                'errors'  => [],
            ]);
    }

    // -------------------------------------------------------------------------
    // 403 — forbidden (accessing another user's resource)
    // -------------------------------------------------------------------------

    public function test_403_returns_json_when_accessing_another_users_resource(): void
    {
        $owner = $this->createUserWithCategories();
        $category = $owner->categories()->first();

        $this->actingAsUser(); // different user

        $this->deleteJson("/api/v1/categories/{$category->id}")
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
                'errors'  => [],
            ]);
    }

    // -------------------------------------------------------------------------
    // 404 — model not found (route model binding)
    // -------------------------------------------------------------------------

    public function test_404_returns_json_for_missing_model(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/v1/transactions/99999')
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found',
                'errors'  => [],
            ]);
    }
}
