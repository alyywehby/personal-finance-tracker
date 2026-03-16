<?php
namespace App\Jobs;

use App\Mail\MonthlySummaryMail;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMonthlySummaryJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public User $user,
        public int $year,
        public int $month
    ) {}

    public function handle(ReportService $reportService): void
    {
        $summary = $reportService->getMonthlySummary($this->user->id, $this->year, $this->month);
        $topCategories = $reportService->getTopCategories($this->user->id, $this->year, $this->month, 5);

        \Mail::to($this->user->email)->send(
            new MonthlySummaryMail($this->user, $summary, $topCategories, $this->year, $this->month)
        );
    }
}
