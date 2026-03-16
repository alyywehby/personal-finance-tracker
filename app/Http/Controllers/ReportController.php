<?php
namespace App\Http\Controllers;

use App\Jobs\SendMonthlySummaryJob;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function index(Request $request)
    {
        $year = (int) ($request->year ?? now()->year);
        $month = (int) ($request->month ?? now()->month);

        $summary = $this->reportService->getMonthlySummary(auth()->id(), $year, $month);
        $dailySpending = $this->reportService->getDailySpending(auth()->id(), $year, $month);

        // Generate month options for the last 12 months
        $monthOptions = [];
        for ($i = 0; $i < 12; $i++) {
            $d = now()->subMonths($i);
            $monthOptions[] = [
                'year' => $d->year,
                'month' => $d->month,
                'label' => $d->format('F Y'),
            ];
        }

        return view('reports.index', compact('summary', 'dailySpending', 'monthOptions', 'year', 'month'));
    }

    public function sendSummary(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2099',
            'month' => 'required|integer|min:1|max:12',
        ]);

        SendMonthlySummaryJob::dispatch(auth()->user(), (int) $request->year, (int) $request->month);

        return response()->json([
            'success' => true,
            'message' => __('app.email_queued'),
        ]);
    }
}
