@extends('layouts.app')

@section('title', __('app.dashboard'))

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">
        {{ __('app.welcome') }}, {{ auth()->user()->name }} 👋
    </h1>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500 mb-1">{{ __('app.total_income') }}</p>
            <p class="text-2xl font-bold text-green-600">${{ number_format($monthlyIncome, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('app.this_month') }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500 mb-1">{{ __('app.total_expenses') }}</p>
            <p class="text-2xl font-bold text-red-500">${{ number_format($monthlyExpenses, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('app.this_month') }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500 mb-1">{{ __('app.net_balance') }}</p>
            <p class="text-2xl font-bold {{ $netBalance >= 0 ? 'text-indigo-600' : 'text-red-500' }}">
                ${{ number_format(abs($netBalance), 2) }}
                @if($netBalance < 0)<span class="text-sm font-normal">-</span>@endif
            </p>
            <p class="text-xs text-gray-400 mt-1">{{ __('app.this_month') }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500 mb-1">{{ __('app.transactions') }}</p>
            <p class="text-2xl font-bold text-gray-700">{{ auth()->user()->transactions()->whereYear('transaction_date', now()->year)->whereMonth('transaction_date', now()->month)->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ __('app.this_month') }}</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        <!-- Bar Chart: 6 months -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <h2 class="text-base font-semibold text-gray-800 mb-4">{{ __('app.income_vs_expenses') }}</h2>
            <canvas id="barChart" class="w-full" height="200"></canvas>
        </div>
        <!-- Pie Chart: by category -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <h2 class="text-base font-semibold text-gray-800 mb-4">{{ __('app.expenses_by_category') }}</h2>
            @if($categoryExpenses->isEmpty())
                <p class="text-sm text-gray-400 text-center py-12">{{ __('app.no_data') }}</p>
            @else
                <canvas id="pieChart" class="w-full" height="200"></canvas>
            @endif
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800">{{ __('app.recent_transactions') }}</h2>
            <a href="/transactions" class="text-sm text-indigo-600 hover:underline">{{ __('app.view_all') }}</a>
        </div>
        @if($recentTransactions->isEmpty())
            <p class="text-sm text-gray-400">{{ __('app.no_transactions') }}</p>
        @else
            <div class="space-y-3">
                @foreach($recentTransactions as $t)
                <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-lg flex-shrink-0"
                        style="background-color: {{ $t->category->color ?? '#6366f1' }}22">
                        {{ $t->category->icon ?? '💳' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $t->description ?: $t->category->name }}</p>
                        <p class="text-xs text-gray-400">{{ $t->transaction_date->format('M d, Y') }} · {{ $t->category->name }}</p>
                    </div>
                    <span class="text-sm font-semibold {{ $t->type === 'income' ? 'text-green-600' : 'text-red-500' }} flex-shrink-0">
                        {{ $t->type === 'income' ? '+' : '-' }}${{ number_format($t->amount, 2) }}
                    </span>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const locale = window.appLocale;
const sixMonths = @json($sixMonths);
const categoryExpenses = @json($categoryExpenses);

// Bar Chart
const barCtx = document.getElementById('barChart');
if (barCtx) {
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: sixMonths.map(m => m.label),
            datasets: [
                {
                    label: '{{ __("app.income") }}',
                    data: sixMonths.map(m => m.income),
                    backgroundColor: '#10B98133',
                    borderColor: '#10B981',
                    borderWidth: 2,
                    borderRadius: 4,
                },
                {
                    label: '{{ __("app.expense") }}',
                    data: sixMonths.map(m => m.expense),
                    backgroundColor: '#EF444433',
                    borderColor: '#EF4444',
                    borderWidth: 2,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });
}

// Pie Chart
const pieCtx = document.getElementById('pieChart');
if (pieCtx && categoryExpenses.length > 0) {
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: categoryExpenses.map(c => c.name),
            datasets: [{
                data: categoryExpenses.map(c => c.total),
                backgroundColor: categoryExpenses.map(c => c.color),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'right' } }
        }
    });
}
</script>
@endpush
