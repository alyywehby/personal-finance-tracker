@extends('layouts.app')

@section('title', __('app.transactions'))

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.transactions') }}</h1>
        <div class="flex gap-2 flex-wrap">
            <a href="/transactions/export?{{ http_build_query($filters) }}"
                class="min-h-[44px] px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                {{ __('app.export_csv') }}
            </a>
            <a href="/transactions/create"
                class="min-h-[44px] px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition">
                + {{ __('app.add_transaction') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-6 border border-gray-100">
        <form method="GET" action="/transactions" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.category') }}</label>
                <select name="category_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 min-h-[44px]">
                    <option value="">{{ __('app.all_categories') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.type') }}</label>
                <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 min-h-[44px]">
                    <option value="">{{ __('app.all_types') }}</option>
                    <option value="income" {{ ($filters['type'] ?? '') === 'income' ? 'selected' : '' }}>{{ __('app.income') }}</option>
                    <option value="expense" {{ ($filters['type'] ?? '') === 'expense' ? 'selected' : '' }}>{{ __('app.expense') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.from_date') }}</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" dir="ltr"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 min-h-[44px]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('app.to_date') }}</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" dir="ltr"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 min-h-[44px]">
            </div>
            <button type="submit" class="min-h-[44px] px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                {{ __('app.filter') }}
            </button>
            <a href="/transactions" class="min-h-[44px] px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition flex items-center">
                {{ __('app.reset') }}
            </a>
        </form>
    </div>

    <!-- Desktop Table -->
    <div class="hidden sm:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-start text-xs font-semibold text-gray-500 uppercase">{{ __('app.date') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold text-gray-500 uppercase">{{ __('app.description') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold text-gray-500 uppercase">{{ __('app.category') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold text-gray-500 uppercase">{{ __('app.type') }}</th>
                        <th class="px-6 py-3 text-end text-xs font-semibold text-gray-500 uppercase">{{ __('app.amount') }}</th>
                        <th class="px-6 py-3 text-end text-xs font-semibold text-gray-500 uppercase">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap" dir="ltr">{{ $t->transaction_date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $t->description ?: '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                                style="background-color: {{ $t->category->color }}22; color: {{ $t->category->color }}">
                                {{ $t->category->icon ?? '' }} {{ $t->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $t->type === 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ __('app.' . $t->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-end font-semibold {{ $t->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
                            {{ $t->type === 'income' ? '+' : '-' }}${{ number_format($t->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-end">
                            <div class="flex items-center justify-end gap-2">
                                <a href="/transactions/{{ $t->id }}/edit"
                                    class="min-w-[44px] min-h-[44px] flex items-center justify-center text-gray-400 hover:text-indigo-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="/transactions/{{ $t->id }}"
                                    onsubmit="return confirm('{{ __('app.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="min-w-[44px] min-h-[44px] flex items-center justify-center text-gray-400 hover:text-red-500 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">{{ __('app.no_transactions') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card List -->
    <div class="sm:hidden space-y-3">
        @forelse($transactions as $t)
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg flex-shrink-0"
                    style="background-color: {{ $t->category->color }}22">
                    {{ $t->category->icon ?? '💳' }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-800">{{ $t->description ?: $t->category->name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5" dir="ltr">{{ $t->transaction_date->format('Y-m-d') }} · {{ $t->category->name }}</p>
                </div>
                <span class="font-bold text-sm {{ $t->type === 'income' ? 'text-green-600' : 'text-red-500' }} flex-shrink-0">
                    {{ $t->type === 'income' ? '+' : '-' }}${{ number_format($t->amount, 2) }}
                </span>
            </div>
            <div class="flex gap-2 mt-3 pt-3 border-t border-gray-50">
                <a href="/transactions/{{ $t->id }}/edit" class="flex-1 py-2 text-center text-sm text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50">{{ __('app.edit') }}</a>
                <form method="POST" action="/transactions/{{ $t->id }}" class="flex-1"
                    onsubmit="return confirm('{{ __('app.confirm_delete') }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full py-2 text-sm text-red-500 border border-red-200 rounded-lg hover:bg-red-50">{{ __('app.delete') }}</button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl p-12 text-center text-gray-400">{{ __('app.no_transactions') }}</div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($transactions->hasPages())
    <div class="mt-6">{{ $transactions->links() }}</div>
    @endif
</div>
@endsection
