@extends('layouts.guest')

@section('title', __('app.sign_in'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-indigo-600">💰 {{ __('app.app_name') }}</h1>
            <p class="mt-2 text-gray-600">{{ __('app.sign_in') }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="/login">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="demo@finance.app">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.password') }}</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600">
                            <span class="text-sm text-gray-600">{{ __('app.remember_me') }}</span>
                        </label>
                    </div>
                    <button type="submit"
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                        {{ __('app.sign_in') }}
                    </button>
                </div>
            </form>

            <p class="mt-6 text-center text-sm text-gray-600">
                {{ __('app.dont_have_account') }}
                <a href="/register" class="text-indigo-600 hover:underline font-medium">{{ __('app.sign_up') }}</a>
            </p>
        </div>
    </div>
</div>
@endsection
