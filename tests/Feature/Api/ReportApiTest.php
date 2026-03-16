<?php
namespace Tests\Feature\Api;

use App\Jobs\SendMonthlySummaryJob;
use App\Mail\MonthlySummaryMail;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_report_returns_correct_totals(): void
    {
        $user = $this->actingAsUser();
        $cat = Category::factory()->create(['user_id' => $user->id]);

        Transaction::factory()->create([
            'user_id' => $user->id, 'category_id' => $cat->id,
            'type' => 'income', 'amount' => 1000.00,
            'transaction_date' => '2024-03-10',
        ]);
        Transaction::factory()->create([
            'user_id' => $user->id, 'category_id' => $cat->id,
            'type' => 'expense', 'amount' => 400.00,
            'transaction_date' => '2024-03-15',
        ]);

        $response = $this->getJson('/api/v1/reports/monthly?year=2024&month=3');
        $response->assertOk()->assertJson(['success' => true]);

        $data = $response->json('data');
        $this->assertEquals(1000.00, $data['total_income']);
        $this->assertEquals(400.00, $data['total_expenses']);
        $this->assertEquals(600.00, $data['net_balance']);
    }

    public function test_monthly_report_returns_breakdown_by_category(): void
    {
        $user = $this->actingAsUser();
        $cat = Category::factory()->create(['user_id' => $user->id, 'name' => 'Food']);

        Transaction::factory()->create([
            'user_id' => $user->id, 'category_id' => $cat->id,
            'type' => 'expense', 'amount' => 200.00,
            'transaction_date' => '2024-03-10',
        ]);

        $response = $this->getJson('/api/v1/reports/monthly?year=2024&month=3');
        $data = $response->json('data');

        $this->assertArrayHasKey('by_category', $data);
        $this->assertNotEmpty($data['by_category']);
        $this->assertEquals('Food', $data['by_category'][0]['name']);
    }

    public function test_monthly_report_returns_zero_for_empty_month(): void
    {
        $this->actingAsUser();

        $response = $this->getJson('/api/v1/reports/monthly?year=2020&month=1');
        $data = $response->json('data');

        $this->assertEquals(0.0, $data['total_income']);
        $this->assertEquals(0.0, $data['total_expenses']);
        $this->assertEquals(0.0, $data['net_balance']);
    }

    public function test_summary_returns_six_months_data(): void
    {
        $this->actingAsUser();

        $response = $this->getJson('/api/v1/reports/summary');
        $response->assertOk();

        $data = $response->json('data');
        $this->assertCount(6, $data);
        $this->assertArrayHasKey('label', $data[0]);
        $this->assertArrayHasKey('total_income', $data[0]);
        $this->assertArrayHasKey('total_expenses', $data[0]);
    }

    public function test_send_summary_queues_job(): void
    {
        Queue::fake();
        $this->actingAsUser();

        $response = $this->postJson('/api/v1/reports/send-summary', [
            'year' => 2024,
            'month' => 3,
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        Queue::assertPushed(SendMonthlySummaryJob::class);
    }

    public function test_send_summary_dispatches_correct_mailable(): void
    {
        Mail::fake();
        $user = $this->actingAsUser();
        $cat = Category::factory()->create(['user_id' => $user->id]);
        Transaction::factory()->create([
            'user_id' => $user->id, 'category_id' => $cat->id,
            'type' => 'expense', 'amount' => 100.00,
            'transaction_date' => '2024-03-10',
        ]);

        // Dispatch directly (sync in test env)
        $job = new SendMonthlySummaryJob($user, 2024, 3);
        $job->handle(app(\App\Services\ReportService::class));

        Mail::assertSent(MonthlySummaryMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
