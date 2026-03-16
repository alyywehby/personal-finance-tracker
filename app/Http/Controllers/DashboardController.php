<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $now = now();
        $year = $now->year;
        $month = $now->month;

        // Current month summary
        $monthlyIncome = Transaction::where('user_id', $user->id)
            ->byMonth($year, $month)
            ->byType('income')
            ->sum('amount');

        $monthlyExpenses = Transaction::where('user_id', $user->id)
            ->byMonth($year, $month)
            ->byType('expense')
            ->sum('amount');

        $netBalance = $monthlyIncome - $monthlyExpenses;

        // Recent 5 transactions
        $recentTransactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Last 6 months bar chart data
        $sixMonths = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $y = $date->year;
            $m = $date->month;
            $income = Transaction::where('user_id', $user->id)->byMonth($y, $m)->byType('income')->sum('amount');
            $expense = Transaction::where('user_id', $user->id)->byMonth($y, $m)->byType('expense')->sum('amount');
            $sixMonths[] = [
                'label' => $date->format('M Y'),
                'income' => (float) $income,
                'expense' => (float) $expense,
            ];
        }

        // Pie chart: expenses by category this month
        $categoryExpenses = Transaction::with('category')
            ->where('user_id', $user->id)
            ->byMonth($year, $month)
            ->byType('expense')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get()
            ->map(fn($t) => [
                'name' => $t->category->name ?? 'Unknown',
                'color' => $t->category->color ?? '#6366f1',
                'total' => (float) $t->total,
            ]);

        return view('dashboard.index', compact(
            'monthlyIncome', 'monthlyExpenses', 'netBalance',
            'recentTransactions', 'sixMonths', 'categoryExpenses'
        ));
    }
}
