<?php
namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_own_categories(): void
    {
        $user = $this->actingAsUser();
        Category::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/v1/categories');
        $response->assertOk()->assertJson(['success' => true]);

        $data = $response->json('data');
        $this->assertCount(3, $data);
    }

    public function test_user_can_create_category(): void
    {
        $this->actingAsUser();

        $response = $this->postJson('/api/v1/categories', [
            'name' => 'New Category',
            'color' => '#FF5733',
            'icon' => '🚀',
        ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('categories', ['name' => 'New Category', 'color' => '#FF5733']);
    }

    public function test_create_category_fails_with_duplicate_name(): void
    {
        $user = $this->actingAsUser();
        Category::factory()->create(['user_id' => $user->id, 'name' => 'Existing']);

        $response = $this->postJson('/api/v1/categories', [
            'name' => 'Existing',
            'color' => '#FF5733',
        ]);

        $response->assertStatus(422);
    }

    public function test_create_category_fails_with_invalid_color(): void
    {
        $this->actingAsUser();

        $response = $this->postJson('/api/v1/categories', [
            'name' => 'Test',
            'color' => 'not-a-hex',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_update_own_category(): void
    {
        $user = $this->actingAsUser();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson("/api/v1/categories/{$category->id}", [
            'name' => 'Updated Name',
            'color' => '#123456',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Updated Name']);
    }

    public function test_user_can_delete_category_without_transactions(): void
    {
        $user = $this->actingAsUser();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/v1/categories/{$category->id}");
        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_delete_category_with_transactions_returns_error(): void
    {
        $user = $this->actingAsUser();
        $category = Category::factory()->create(['user_id' => $user->id]);
        Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);

        $response = $this->deleteJson("/api/v1/categories/{$category->id}");
        $response->assertStatus(422)->assertJson(['success' => false]);
    }

    public function test_user_cannot_access_others_categories(): void
    {
        $this->actingAsUser();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson("/api/v1/categories/{$category->id}");
        $response->assertStatus(403);
    }
}
