@extends('layouts.app')

@section('title', config('app.name') . ' - ' . __('auth.create_float_type'))

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
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90 sm:text-xl">
                                    {{ __('auth.create_float_type') }}</h2>
                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('auth.add_new_float_type') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('float-types.index') }}"
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
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                            {{ __('auth.float_type_details') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('auth.basic_float_type_info') }}</p>
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

                        <form method="POST" action="{{ route('float-types.store') }}" class="space-y-6">
                            @csrf

                            <!-- Float Type Name and Code -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- Float Type Name Field -->
                                <div class="space-y-2">
                                    <label for="name"
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        {{ __('auth.float_type_name') }}
                                        <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                                            required
                                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:border-brand-500 @error('name') border-error-500 focus:border-error-500 focus:ring-error-500/20 @enderror"
                                            placeholder="{{ __('auth.enter_name') }}">
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

                                <!-- Float Type Code Field -->
                                <div class="space-y-2">
                                    <label for="code"
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                        {{ __('auth.float_type_code') }}
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="code" name="code" value="{{ old('code') }}"
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

                            <!-- Description -->
                            <div class="space-y-2">
                                <label for="description"
                                    class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16m-7 6h7" />
                                    </svg>
                                    {{ __('auth.description') }}
                                </label>
                                <div class="relative">
                                    <textarea id="description" name="description" rows="3"
                                        class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:border-brand-500 @error('description') border-error-500 focus:border-error-500 focus:ring-error-500/20 @enderror"
                                        placeholder="Enter description">{{ old('description') }}</textarea>
                                </div>
                                @error('description')
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
                            <div x-data="{ switcherToggle: {{ old('is_active', true) ? 'true' : 'false' }} }">
                                <label for="is_active"
                                    class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700 select-none dark:text-gray-400">
                                    <div class="relative">
                                        <input type="checkbox" name="is_active" value="1" id="is_active"
                                            {{ old('is_active', true) ? 'checked' : '' }} class="sr-only"
                                            @change="switcherToggle = !switcherToggle" />
                                        <div class="block h-6 w-11 rounded-full"
                                            :class="switcherToggle ? 'bg-brand-500 dark:bg-brand-500' :
                                                'bg-gray-200 dark:bg-white/10'">
                                        </div>
                                        <div :class="switcherToggle ? 'translate-x-full' : 'translate-x-0'"
                                            class="shadow-theme-sm absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white duration-300 ease-linear">
                                        </div>
                                    </div>
                                    <span x-show="switcherToggle">{{ __('auth.active') }}</span>
                                    <span x-show="!switcherToggle">{{ __('auth.inactive') }}</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('auth.make_float_type_inactive') }}
                                </p>
                            </div>

                            <!-- Form Actions -->
                            <div
                                class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 dark:border-gray-800 sm:flex-row sm:justify-end">
                                <a href="{{ route('float-types.index') }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.05]">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:ring-offset-2 dark:ring-offset-gray-900">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    {{ __('auth.create_float_type') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
