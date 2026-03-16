<?php
namespace App\Console\Commands;

use App\Jobs\SendMonthlySummaryJob;
use App\Models\User;
use Illuminate\Console\Command;

class SendMonthlySummaryCommand extends Command
{
    protected $signature = 'finance:send-monthly-summary';
    protected $description = 'Send monthly financial summary email to all users';

    public function handle(): int
    {
        $previousMonth = now()->subMonth();
        $year = $previousMonth->year;
        $month = $previousMonth->month;

        $count = 0;
        User::chunk(100, function ($users) use ($year, $month, &$count) {
            foreach ($users as $user) {
                SendMonthlySummaryJob::dispatch($user, $year, $month);
                $count++;
            }
        });

        $this->info("Dispatched monthly summary jobs for {$count} users.");
        return Command::SUCCESS;
    }
}
