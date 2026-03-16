@extends('layouts.app')

@section('title', __('app.reports'))

@section('content')
<div class="max-w-7xl mx-auto" x-data="{
    sending: false,
    toast: '',
    toastType: 'success',
    async sendEmail() {
        this.sending = true;
        try {
            const res = await fetch('/reports/send-summary', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ year: {{ $year }}, month: {{ $month }} })
            });
            const data = await res.json();
            this.toast = data.message;
            this.toastType = data.success ? 'success' : 'error';
            setTimeout(() => this.toast = '', 4000);
        } catch(e) {
            this.toast = '{{ __('app.error') }}';
            this.toastType = 'error';
            setTimeout(() => this.toast = '', 4000);
        } finally {
            this.sending = false;
        }
    }
}">
    <!-- Toast -->
    <div x-show="toast" x-cloak
        :class="toastType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
        class="fixed top-4 end-4 z-50 p-4 rounded-xl border shadow-lg text-sm font-medium"
        x-text="toast"></div>

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.reports') }}</h1>
        <button @click="sendEmail()" :disabled="sending"
            class="min-h-[44px] px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2">
            <svg class="w-4 h-4" :class="sending ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            {{ __('app.send_summary_email') }}
        </button>
    </div>

    <!-- Month Selector -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-6 border border-gray-100">
        <form method="GET" action="/reports" class="flex items-center gap-3 flex-wrap">
            <label class="text-sm font-medium text-gray-700">{{ __('app.select_month') }}</label>
            <select name="year" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 min-h-[44px]">
                @foreach(range(now()->year - 2, now()->year) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <select name="month" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 min-h-[44px]">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m)->format('F') }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="min-h-[44px] px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition">
                {{ __('app.filter') }}
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500">{{ __('app.total_income') }}</p>
            <p class="text-2xl font-bold text-green-600 mt-1">${{ number_format($summary['total_income'], 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500">{{ __('app.total_expenses') }}</p>
            <p class="text-2xl font-bold text-red-500 mt-1">${{ number_format($summary['total_expenses'], 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500">{{ __('app.net_balance') }}</p>
            <p class="text-2xl font-bold mt-1 {{ $summary['net_balance'] >= 0 ? 'text-indigo-600' : 'text-red-500' }}">
                ${{ number_format(abs($summary['net_balance']), 2) }}
                @if($summary['net_balance'] < 0)<span class="text-sm font-normal">(negative)</span>@endif
            </p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <h2 class="text-base font-semibold text-gray-800 mb-4">{{ __('app.daily_spending') }}</h2>
            @if(empty($dailySpending))
                <p class="text-sm text-gray-400 text-center py-12">{{ __('app.no_data') }}</p>
            @else
                <canvas id="dailyChart" height="200"></canvas>
            @endif
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <h2 class="text-base font-semibold text-gray-800 mb-4">{{ __('app.by_category') }}</h2>
            @if(empty($summary['by_category']))
                <p class="text-sm text-gray-400 text-center py-12">{{ __('app.no_data') }}</p>
            @else
                <canvas id="categoryPieChart" height="200"></canvas>
            @endif
        </div>
    </div>

    <!-- Category Breakdown Table -->
    @if(!empty($summary['by_category']))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="text-base font-semibold text-gray-800">{{ __('app.by_category') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-start text-xs font-semibold text-gray-500 uppercase">{{ __('app.category') }}</th>
                        <th class="px-6 py-3 text-end text-xs font-semibold text-gray-500 uppercase">{{ __('app.amount') }}</th>
                        <th class="px-6 py-3 text-end text-xs font-semibold text-gray-500 uppercase">{{ __('app.percentage') }}</th>
                        <th class="px-6 py-3 text-end text-xs font-semibold text-gray-500 uppercase">{{ __('app.transaction_count') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($summary['by_category'] as $cat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $cat['color'] }}"></div>
                                <span class="font-medium text-gray-800">{{ $cat['icon'] }} {{ $cat['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-end font-semibold text-gray-700">${{ number_format($cat['total'], 2) }}</td>
                        <td class="px-6 py-4 text-end">
                            <span class="text-gray-600">{{ $cat['percentage'] }}%</span>
                            <div class="w-24 bg-gray-100 rounded-full h-1.5 mt-1 ms-auto">
                                <div class="h-1.5 rounded-full" style="width: {{ $cat['percentage'] }}%; background-color: {{ $cat['color'] }}"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-end text-gray-600">{{ $cat['count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const dailyData = @json($dailySpending);
const categoryData = @json($summary['by_category'] ?? []);

if (dailyData.length > 0) {
    new Chart(document.getElementById('dailyChart'), {
        type: 'bar',
        data: {
            labels: dailyData.map(d => d.day),
            datasets: [{
                label: '{{ __("app.expense") }}',
                data: dailyData.map(d => d.total),
                backgroundColor: '#EF444433',
                borderColor: '#EF4444',
                borderWidth: 2,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
}

if (categoryData.length > 0) {
    new Chart(document.getElementById('categoryPieChart'), {
        type: 'doughnut',
        data: {
            labels: categoryData.map(c => c.name),
            datasets: [{
                data: categoryData.map(c => c.total),
                backgroundColor: categoryData.map(c => c.color),
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
