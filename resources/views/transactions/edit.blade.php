@extends('layouts.app')

@section('title', __('app.edit_transaction'))

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="/transactions" class="text-sm text-indigo-600 hover:underline flex items-center gap-1 rtl:flex-row-reverse">
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('app.transactions') }}
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ __('app.edit_transaction') }}</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                @foreach($errors->all() as $error)
                    <p class="text-sm text-red-600">{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <form method="POST" action="/transactions/{{ $transaction->id }}">
            @csrf @method('PUT')
            @include('transactions._form', ['transaction' => $transaction])
            <div class="flex gap-3 mt-6">
                <a href="/transactions" class="flex-1 py-3 text-center border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">{{ __('app.cancel') }}</a>
                <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">{{ __('app.save') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
