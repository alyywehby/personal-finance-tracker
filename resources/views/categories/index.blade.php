@extends('layouts.app')

@section('title', __('app.categories'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.categories') }}</h1>
        <button onclick="document.getElementById('addCategoryModal').classList.remove('hidden')"
            class="min-h-[44px] px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition">
            + {{ __('app.add_category') }}
        </button>
    </div>

    @if($categories->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-100">
            <p class="text-gray-400">{{ __('app.no_categories') }}</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="grid grid-cols-1 divide-y divide-gray-50">
                @foreach($categories as $category)
                <div class="flex items-center gap-4 px-6 py-4" x-data="{ editing: false }">
                    <!-- Color + Icon -->
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg flex-shrink-0"
                        style="background-color: {{ $category->color }}22; border: 2px solid {{ $category->color }}">
                        {{ $category->icon ?? '📦' }}
                    </div>

                    <!-- Name -->
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800">{{ $category->name }}</p>
                        <p class="text-xs text-gray-400">{{ $category->transactions_count }} {{ __('app.transactions') }}</p>
                    </div>

                    <!-- Color swatch -->
                    <span class="hidden sm:block px-2 py-1 text-xs rounded font-mono"
                        style="background-color: {{ $category->color }}22; color: {{ $category->color }}">
                        {{ $category->color }}
                    </span>

                    <!-- Actions -->
                    <div class="flex gap-2 flex-shrink-0">
                        <button @click="editing = true"
                            class="min-h-[44px] min-w-[44px] flex items-center justify-center text-gray-400 hover:text-indigo-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form method="POST" action="/categories/{{ $category->id }}"
                            onsubmit="return confirm('{{ __('app.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="min-h-[44px] min-w-[44px] flex items-center justify-center text-gray-400 hover:text-red-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>

                    <!-- Edit Modal (inline) -->
                    <div x-show="editing" x-cloak class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
                        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.stop>
                            <h3 class="text-lg font-semibold mb-4">{{ __('app.edit_category') }}</h3>
                            <form method="POST" action="/categories/{{ $category->id }}">
                                @csrf @method('PUT')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.name') }}</label>
                                        <input type="text" name="name" value="{{ $category->name }}" required maxlength="50"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.color') }}</label>
                                        <input type="color" name="color" value="{{ $category->color }}" required
                                            class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.icon') }}</label>
                                        <x-emoji-picker name="icon" :value="$category->icon" />
                                    </div>
                                    <div class="flex gap-3 pt-2">
                                        <button type="button" @click="editing = false"
                                            class="flex-1 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                            {{ __('app.cancel') }}
                                        </button>
                                        <button type="submit"
                                            class="flex-1 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                            {{ __('app.save') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6" onclick="event.stopPropagation()">
        <h3 class="text-lg font-semibold mb-4">{{ __('app.add_category') }}</h3>
        <form method="POST" action="/categories">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.name') }} *</label>
                    <input type="text" name="name" required maxlength="50"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.color') }} *</label>
                    <input type="color" name="color" value="#6366f1" required
                        class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.icon') }}</label>
                    <x-emoji-picker name="icon" />
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('addCategoryModal').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        {{ __('app.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
