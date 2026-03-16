<?php
namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getMonthlySummary(int $userId, int $year, int $month): array
    {
        $income = Transaction::where('user_id', $userId)
            ->byMonth($year, $month)
            ->byType('income')
            ->sum('amount');

        $expenses = Transaction::where('user_id', $userId)
            ->byMonth($year, $month)
            ->byType('expense')
            ->sum('amount');

        $byCategory = Transaction::with('category')
            ->where('user_id', $userId)
            ->byMonth($year, $month)
            ->byType('expense')
            ->select('category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get()
            ->map(fn($t) => [
                'category_id' => $t->category_id,
                'name' => $t->category->name ?? 'Unknown',
                'color' => $t->category->color ?? '#6366f1',
                'icon' => $t->category->icon ?? '',
                'total' => (float) $t->total,
                'count' => (int) $t->count,
                'percentage' => $expenses > 0 ? round(($t->total / $expenses) * 100, 1) : 0,
            ]);

        return [
            'year' => $year,
            'month' => $month,
            'total_income' => (float) $income,
            'total_expenses' => (float) $expenses,
            'net_balance' => (float) ($income - $expenses),
            'by_category' => $byCategory,
        ];
    }

    public function getDailySpending(int $userId, int $year, int $month): array
    {
        return Transaction::where('user_id', $userId)
            ->byMonth($year, $month)
            ->byType('expense')
            ->select(DB::raw('DATE(transaction_date) as day'), DB::raw('SUM(amount) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn($t) => [
                'day' => $t->day,
                'total' => (float) $t->total,
            ])
            ->toArray();
    }

    public function getSixMonthsSummary(int $userId): array
    {
        $result = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $y = $date->year;
            $m = $date->month;
            $income = Transaction::where('user_id', $userId)->byMonth($y, $m)->byType('income')->sum('amount');
            $expense = Transaction::where('user_id', $userId)->byMonth($y, $m)->byType('expense')->sum('amount');
            $result[] = [
                'year' => $y,
                'month' => $m,
                'label' => $date->format('M Y'),
                'total_income' => (float) $income,
                'total_expenses' => (float) $expense,
                'net_balance' => (float) ($income - $expense),
            ];
        }
        return $result;
    }

    public function getTopCategories(int $userId, int $year, int $month, int $limit = 5): array
    {
        return Transaction::with('category')
            ->where('user_id', $userId)
            ->byMonth($year, $month)
            ->byType('expense')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn($t) => [
                'name' => $t->category->name ?? 'Unknown',
                'color' => $t->category->color ?? '#6366f1',
                'total' => (float) $t->total,
            ])
            ->toArray();
    }
}
