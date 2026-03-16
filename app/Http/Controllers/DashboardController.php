<?php
namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\TransactionService;

class DashboardController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private TransactionService $transactionService,
    ) {}

    public function index()
    {
        $userId = auth()->id();
        $now = now();

        $summary = $this->reportService->getMonthlySummary($userId, $now->year, $now->month);
        $monthlyIncome = $summary['total_income'];
        $monthlyExpenses = $summary['total_expenses'];
        $netBalance = $summary['net_balance'];
        $categoryExpenses = collect($summary['by_category']);

        $recentTransactions = $this->transactionService->getRecentTransactions($userId);

        $sixMonths = array_map(fn($m) => [
            'label'   => $m['label'],
            'income'  => $m['total_income'],
            'expense' => $m['total_expenses'],
        ], $this->reportService->getSixMonthsSummary($userId));

        return view('dashboard.index', compact(
            'monthlyIncome', 'monthlyExpenses', 'netBalance',
            'recentTransactions', 'sixMonths', 'categoryExpenses'
        ));
    }
}
