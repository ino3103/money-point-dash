@extends('auth.app')

@section('content')
    <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center">
        <div class="mb-5 sm:mb-8">
            <h1 class="text-title-sm sm:text-title-md mb-2 font-semibold text-gray-800 dark:text-white/90">
                {{ __('auth.forgot_your_password') }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('auth.forgot_password_description') }}
            </p>
        </div>
        <div>
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            {{ __('auth.email') }}<span class="text-error-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" placeholder="{{ __('auth.enter_your_email') }}"
                            value="{{ old('email') }}" required autofocus
                            class="dark:bg-dark-900 font-noraml shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-left text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('email') border-error-500 @enderror" />
                        @error('email')
                            <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Button -->
                    <div>
                        <button
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                            {{ __('auth.send_reset_link') }}
                        </button>
                    </div>
                </div>
            </form>
            <div class="mt-5">
                <p class="text-center text-sm font-normal text-gray-700 sm:text-start dark:text-gray-400">
                    {{ __('auth.remember_password') }}
                    <a href="{{ route('login') }}" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">{{ __('auth.click_here') }}</a>
                </p>
            </div>
        </div>
    </div>
@endsection
