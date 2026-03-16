@extends('layouts.guest')

@section('title', __('app.sign_up'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-indigo-600">💰 {{ __('app.app_name') }}</h1>
            <p class="mt-2 text-gray-600">{{ __('app.sign_up') }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="/register">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.name') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.password') }}</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.confirm_password') }}</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                    <button type="submit"
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                        {{ __('app.sign_up') }}
                    </button>
                </div>
            </form>

            <p class="mt-6 text-center text-sm text-gray-600">
                {{ __('app.already_have_account') }}
                <a href="/login" class="text-indigo-600 hover:underline font-medium">{{ __('app.sign_in') }}</a>
            </p>
        </div>
    </div>
</div>
@endsection
