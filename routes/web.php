<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

// Locale switcher (works for guests and auth users)
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect('/dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
    Route::resource('/transactions', TransactionController::class);

    Route::resource('/categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/send-summary', [ReportController::class, 'sendSummary'])->name('reports.send-summary');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
