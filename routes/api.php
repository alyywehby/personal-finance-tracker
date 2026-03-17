<?php
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\LocaleController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public auth endpoints
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });

    // Protected endpoints
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->name('api.')->group(function () {
        // Transactions
        Route::get('/transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
        Route::apiResource('/transactions', TransactionController::class);

        // Categories
        Route::apiResource('/categories', CategoryController::class)->except(['show']);

        // Reports
        Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('/reports/summary', [ReportController::class, 'summary'])->name('reports.summary');
        Route::post('/reports/send-summary', [ReportController::class, 'sendSummary'])->name('reports.send-summary');

        // User locale
        Route::put('/user/locale', [LocaleController::class, 'update'])->name('user.locale.update');
    });
});
