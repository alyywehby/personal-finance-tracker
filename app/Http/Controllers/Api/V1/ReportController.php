<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\SendMonthlySummaryJob;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function monthly(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2099',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $data = $this->reportService->getMonthlySummary(
            auth()->id(),
            (int) $request->year,
            (int) $request->month
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => '',
        ]);
    }

    public function summary()
    {
        $data = $this->reportService->getSixMonthsSummary(auth()->id());
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => '',
        ]);
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
            'data' => null,
            'message' => 'Email queued successfully',
        ]);
    }
}
