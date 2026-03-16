<?php
namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    private function createTransaction(User $user, array $attrs = []): Transaction
    {
        $category = Category::factory()->create(['user_id' => $user->id]);
        return Transaction::factory()->create(array_merge([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ], $attrs));
    }

    public function test_user_can_list_own_transactions(): void
    {
        $user = $this->actingAsUser();
        $this->createTransaction($user);
        $this->createTransaction($user);

        $response = $this->getJson('/api/v1/transactions');
        $response->assertOk()->assertJson(['success' => true]);

        $data = $response->json('data.data');
        $this->assertCount(2, $data);
    }

    public function test_user_cannot_see_other_users_transactions(): void
    {
        $user = $this->actingAsUser();
        $otherUser = User::factory()->create();
        $this->createTransaction($otherUser);

        $response = $this->getJson('/api/v1/transactions');
        $response->assertOk();

        $data = $response->json('data.data');
        $this->assertCount(0, $data);
    }

    public function test_user_can_create_income_transaction(): void
    {
        $user = $this->actingAsUser();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/v1/transactions', [
            'type' => 'income',
            'amount' => 500.00,
            'category_id' => $category->id,
            'transaction_date' => now()->format('Y-m-d'),
            'description' => 'Salary',
        ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('transactions', ['type' => 'income', 'amount' => 500.00]);
    }

    public function test_user_can_create_expense_transaction(): void
    {
        $user = $this->actingAsUser();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/v1/transactions', [
            'type' => 'expense',
            'amount' => 100.50,
            'category_id' => $category->id,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('transactions', ['type' => 'expense', 'amount' => 100.50]);
    }

    public function test_create_transaction_fails_with_invalid_amount(): void
    {
        $user = $this->actingAsUser();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/v1/transactions', [
            'type' => 'expense',
            'amount' => -10,
            'category_id' => $category->id,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(422);
    }

    public function test_create_transaction_fails_with_invalid_category(): void
    {
        $user = $this->actingAsUser();
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->postJson('/api/v1/transactions', [
            'type' => 'expense',
            'amount' => 50.00,
            'category_id' => $otherCategory->id,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_update_own_transaction(): void
    {
        $user = $this->actingAsUser();
        $transaction = $this->createTransaction($user, ['amount' => 100]);
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson("/api/v1/transactions/{$transaction->id}", [
            'type' => 'income',
            'amount' => 200.00,
            'category_id' => $category->id,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'amount' => 200.00]);
    }

    public function test_user_cannot_update_others_transaction(): void
    {
        $user = $this->actingAsUser();
        $otherUser = User::factory()->create();
        $transaction = $this->createTransaction($otherUser);
        // Use the authenticated user's category to pass validation
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson("/api/v1/transactions/{$transaction->id}", [
            'type' => 'expense',
            'amount' => 50.00,
            'category_id' => $category->id,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_transaction(): void
    {
        $user = $this->actingAsUser();
        $transaction = $this->createTransaction($user);

        $response = $this->deleteJson("/api/v1/transactions/{$transaction->id}");
        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }

    public function test_user_cannot_delete_others_transaction(): void
    {
        $this->actingAsUser();
        $otherUser = User::factory()->create();
        $transaction = $this->createTransaction($otherUser);

        $response = $this->deleteJson("/api/v1/transactions/{$transaction->id}");
        $response->assertStatus(403);
    }

    public function test_transactions_can_be_filtered_by_type(): void
    {
        $user = $this->actingAsUser();
        $this->createTransaction($user, ['type' => 'income']);
        $this->createTransaction($user, ['type' => 'expense']);
        $this->createTransaction($user, ['type' => 'expense']);

        $response = $this->getJson('/api/v1/transactions?type=expense');
        $response->assertOk();

        $data = $response->json('data.data');
        $this->assertCount(2, $data);
        foreach ($data as $t) {
            $this->assertEquals('expense', $t['type']);
        }
    }

    public function test_transactions_can_be_filtered_by_category(): void
    {
        $user = $this->actingAsUser();
        $cat1 = Category::factory()->create(['user_id' => $user->id]);
        $cat2 = Category::factory()->create(['user_id' => $user->id]);

        Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $cat1->id]);
        Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $cat1->id]);
        Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $cat2->id]);

        $response = $this->getJson("/api/v1/transactions?category_id={$cat1->id}");
        $response->assertOk();

        $data = $response->json('data.data');
        $this->assertCount(2, $data);
    }

    public function test_transactions_can_be_filtered_by_date_range(): void
    {
        $user = $this->actingAsUser();
        $cat = Category::factory()->create(['user_id' => $user->id]);

        Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id, 'transaction_date' => '2024-01-15']);
        Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id, 'transaction_date' => '2024-02-15']);
        Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id, 'transaction_date' => '2024-03-15']);

        $response = $this->getJson('/api/v1/transactions?from=2024-01-01&to=2024-02-28');
        $data = $response->json('data.data');
        $this->assertCount(2, $data);
    }

    public function test_export_csv_returns_correct_headers(): void
    {
        $user = $this->actingAsUser();

        $response = $this->get('/api/v1/transactions/export');
        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }

    public function test_export_csv_contains_correct_data(): void
    {
        $user = $this->actingAsUser();

        // Verify categories were created
        $this->assertCount(3, $this->categories, 'Categories should be created by actingAsUser()');
        $cat = $this->categories[0];

        Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'type' => 'expense',
            'amount' => 50.00,
            'description' => 'Lunch',
            'transaction_date' => '2024-03-15',
        ]);

        app()->setLocale('en');

        $response = $this->get('/api/v1/transactions/export');
        $content = $response->getContent();

        // Remove BOM for easier assertion
        $content = str_replace("\xEF\xBB\xBF", '', $content);

        $this->assertStringContainsString('Food', $content);
        $this->assertStringContainsString('50.00', $content);
    }
}
