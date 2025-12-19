@extends('layouts.app')

@section('title', config('app.name') . ' - ' . __('auth.edit_branch'))

@section('content')
    <div class="mx-auto max-w-7xl p-4 pb-20 md:p-6 md:pb-6">
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <div class="col-span-12">
                <!-- Header Card -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-500/10">
                                <svg class="fill-brand-500" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19 21H5C4.44772 21 4 20.5523 4 20V11L1 11L11.3273 1.6115C11.7087 1.26475 12.2913 1.26475 12.6727 1.6115L23 11L20 11V20C20 20.5523 19.5523 21 19 21ZM6 19H18V9.15745L12 3.7029L6 9.15745V19ZM8 15V17H16V15H8Z"
                                        fill="currentColor" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90 sm:text-2xl">
                                    {{ __('auth.edit_branch') }}</h2>
                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('auth.update_branch_info') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('branches.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.05]">
                            <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.0002 12.6666L5.3335 7.99998L10.0002 3.33331" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('auth.back') }}
                        </a>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="mt-4 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <!-- Card Header -->
                    <div
                        class="border-b border-gray-100 bg-gray-50/50 px-5 py-4 dark:border-gray-800 dark:bg-gray-900/50 sm:px-6">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('auth.branch_details') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('auth.basic_branch_info') }}</p>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 sm:p-6">
                        @if (session('error'))
                            <div
                                class="mb-5 rounded-lg border border-error-200 bg-error-50 p-4 text-sm text-error-700 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400">
                                <div class="flex items-start gap-3">
                                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-error-500" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <p class="font-medium">{{ session('error') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div
                                class="mb-5 rounded-lg border border-error-200 bg-error-50 p-4 dark:border-error-800 dark:bg-error-500/10">
                                <div class="flex items-start gap-3">
                                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-error-500" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-error-800 dark:text-error-400 mb-2">
                                            There were some errors with your submission:
                                        </h3>
                                        <ul
                                            class="list-disc list-inside space-y-1 text-sm text-error-700 dark:text-error-300">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('branches.update', $branch) }}" class="space-y-6">
                            @csrf
                            @method('PATCH')

                            <!-- Branch Name and Code -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- Branch Name Field -->
                                <div class="space-y-2">
                                    <label for="name"
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        Branch Name
                                        <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="name" name="name"
                                            value="{{ old('name', $branch->name) }}" required
                                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:border-brand-500 @error('name') border-error-500 focus:border-error-500 focus:ring-error-500/20 @enderror"
                                            placeholder="{{ __('auth.enter_branch_name') }}">
                                    </div>
                                    @error('name')
                                        <p class="mt-1 flex items-center gap-1.5 text-sm text-error-600 dark:text-error-400">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Branch Code Field -->
                                <div class="space-y-2">
                                    <label for="code"
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                        Branch Code
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="code" name="code"
                                            value="{{ old('code', $branch->code) }}"
                                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:border-brand-500 @error('code') border-error-500 focus:border-error-500 focus:ring-error-500/20 @enderror"
                                            placeholder="{{ __('auth.enter_branch_code') }}">
                                    </div>
                                    @error('code')
                                        <p class="mt-1 flex items-center gap-1.5 text-sm text-error-600 dark:text-error-400">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email and Phone -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- Email Field -->
                                <div class="space-y-2">
                                    <label for="email"
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Email
                                    </label>
                                    <div class="relative">
                                        <input type="email" id="email" name="email"
                                            value="{{ old('email', $branch->email) }}"
                                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:border-brand-500 @error('email') border-error-500 focus:border-error-500 focus:ring-error-500/20 @enderror"
                                            placeholder="Enter email address">
                                    </div>
                                    @error('email')
                                        <p class="mt-1 flex items-center gap-1.5 text-sm text-error-600 dark:text-error-400">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Phone Field -->
                                <div class="space-y-2">
                                    <label for="phone"
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ __('auth.phone_number') }}
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="phone" name="phone"
                                            value="{{ old('phone', $branch->phone) }}"
                                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:border-brand-500 @error('phone') border-error-500 focus:border-error-500 focus:ring-error-500/20 @enderror"
                                            placeholder="Enter phone number">
                                    </div>
                                    @error('phone')
                                        <p class="mt-1 flex items-center gap-1.5 text-sm text-error-600 dark:text-error-400">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="space-y-2">
                                <label for="address"
                                    class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ __('auth.address') }}
                                </label>
                                <div class="relative">
                                    <textarea id="address" name="address" rows="3"
                                        class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:border-brand-500 @error('address') border-error-500 focus:border-error-500 focus:ring-error-500/20 @enderror"
                                        placeholder="Enter branch address">{{ old('address', $branch->address) }}</textarea>
                                </div>
                                @error('address')
                                    <p class="mt-1 flex items-center gap-1.5 text-sm text-error-600 dark:text-error-400">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Active Status -->
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                    {{ old('is_active', $branch->is_active) ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-brand-500 transition-colors focus:ring-2 focus:ring-brand-500/20 focus:ring-offset-0 dark:border-gray-600 dark:bg-gray-800 dark:ring-offset-gray-900">
                                <label for="is_active"
                                    class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                    {{ __('auth.active_branch') }}
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('auth.make_branch_inactive') }}
                                </p>
                            </div>

                            <!-- Form Actions -->
                            <div
                                class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 dark:border-gray-800 sm:flex-row sm:justify-end">
                                <a href="{{ route('branches.index') }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.05]">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    {{ __('auth.cancel') }}
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:ring-offset-2 dark:ring-offset-gray-900">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ __('auth.update_branch') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
