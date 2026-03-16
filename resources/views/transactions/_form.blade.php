<div class="space-y-5">
    <!-- Type -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('app.type') }} *</label>
        <div class="flex gap-3">
            <label class="flex-1 flex items-center gap-2 p-3 border-2 rounded-lg cursor-pointer transition
                {{ old('type', $transaction->type ?? '') === 'income' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }}">
                <input type="radio" name="type" value="income" class="text-green-500"
                    {{ old('type', $transaction->type ?? '') === 'income' ? 'checked' : '' }}>
                <span class="text-sm font-medium">💹 {{ __('app.income') }}</span>
            </label>
            <label class="flex-1 flex items-center gap-2 p-3 border-2 rounded-lg cursor-pointer transition
                {{ old('type', $transaction->type ?? '') === 'expense' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300' }}">
                <input type="radio" name="type" value="expense" class="text-red-500"
                    {{ old('type', $transaction->type ?? 'expense') === 'expense' ? 'checked' : '' }}>
                <span class="text-sm font-medium">💸 {{ __('app.expense') }}</span>
            </label>
        </div>
    </div>

    <!-- Amount -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.amount') }} *</label>
        <input type="number" name="amount" step="0.01" min="0.01" required
            value="{{ old('amount', $transaction->amount ?? '') }}"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            placeholder="0.00">
    </div>

    <!-- Category -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.category') }} *</label>
        <select name="category_id" required
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            <option value="">-- {{ __('app.category') }} --</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ old('category_id', $transaction->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->icon }} {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Description -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.description') }}</label>
        <input type="text" name="description" maxlength="255"
            value="{{ old('description', $transaction->description ?? '') }}"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            placeholder="{{ __('app.description') }}">
    </div>

    <!-- Date -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.date') }} *</label>
        <input type="date" name="transaction_date" required dir="ltr"
            value="{{ old('transaction_date', isset($transaction) ? $transaction->transaction_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
    </div>
</div>
