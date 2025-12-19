@extends('layouts.app')

@section('title', config('app.name') . ' - ' . __('auth.edit_user'))

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
                                        d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z"
                                        stroke="currentColor" stroke-width="2" />
                                    <path d="M12 14C8.13401 14 5 17.134 5 21H19C19 17.134 15.866 14 12 14Z"
                                        stroke="currentColor" stroke-width="2" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90 sm:text-xl">
                                    {{ __('auth.edit_user') }}</h2>
                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('auth.update_user_info') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('users.index') }}"
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
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('auth.user_details') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('auth.basic_user_info') }}</p>
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
                                            {{ __('auth.there_were_errors') }}
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

                        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                            @csrf
                            @method('PATCH')

                            <!-- Name and Email -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- Name Field -->
                                <div class="space-y-2">
                                    <label for="name"
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        {{ __('auth.user_name') }}
                                        <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="name" name="name"
                                            value="{{ old('name', $user->name) }}" required
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

                                <!-- Email Field -->
                                <div class="space-y-2">
                                    <label for="email"
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ __('auth.email_address') }}
                                        <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="email" id="email" name="email"
                                            value="{{ old('email', $user->email) }}" required
                                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:border-brand-500 @error('email') border-error-500 focus:border-error-500 focus:ring-error-500/20 @enderror"
                                            placeholder="{{ __('auth.enter_email') }}">
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
                            </div>

                            <!-- NIN and Phone -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- NIN Field -->
                                <div class="space-y-2">
                                    <label for="nin"
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                        </svg>
                                        {{ __('auth.nin') }}
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="nin" name="nin"
                                            value="{{ old('nin', $user->nin) }}"
                                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:border-brand-500 @error('nin') border-error-500 focus:border-error-500 focus:ring-error-500/20 @enderror"
                                            placeholder="{{ __('auth.enter_nin') }}">
                                    </div>
                                    @error('nin')
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
                                        {{ __('auth.phone') }}
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="phone" name="phone"
                                            value="{{ old('phone', $user->phone) }}"
                                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:border-brand-500 @error('phone') border-error-500 focus:border-error-500 focus:ring-error-500/20 @enderror"
                                            placeholder="{{ __('auth.enter_phone') }}">
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

                            <!-- Branch and Role -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- Branch Field (Optional) -->
                                <div class="space-y-2">
                                    <label for="branch_id"
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21H5C4.44772 21 4 20.5523 4 20V11L1 11L11.3273 1.6115C11.7087 1.26475 12.2913 1.26475 12.6727 1.6115L23 11L20 11V20C20 20.5523 19.5523 21 19 21ZM6 19H18V9.15745L12 3.7029L6 9.15745V19ZM8 15V17H16V15H8Z" />
                                        </svg>
                                        {{ __('auth.branch') }}
                                    </label>
                                    <div class="relative">
                                        <select id="branch_id" name="branch_id"
                                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-200 dark:focus:border-brand-500 @error('branch_id') border-error-500 focus:border-error-500 focus:ring-error-500/20 @enderror">
                                            <option value="">{{ __('auth.select_branch') }}</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('branch_id')
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

                                <!-- Role Field (Required) -->
                                <div class="space-y-2">
                                    <label for="role"
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        {{ __('auth.user_role') }}
                                        <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select id="role" name="role" required
                                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-200 dark:focus:border-brand-500 @error('role') border-error-500 focus:border-error-500 focus:ring-error-500/20 @enderror">
                                            <option value="">{{ __('auth.select_role') }}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ old('role', $userRole?->id) == $role->id ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('role')
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

                            <!-- Active Status -->
                            <div
                                class="rounded-lg border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-900/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-500/10">
                                            <svg class="h-5 w-5 text-brand-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                                {{ __('auth.status') }}
                                            </h4>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('auth.active_user_description') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div x-data="{ switcherToggle: {{ old('is_active', $user->is_active) ? 'true' : 'false' }} }">
                                        <label for="is_active_toggle"
                                            class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700 select-none dark:text-gray-400">
                                            <div class="relative">
                                                <input type="checkbox" name="is_active" value="1"
                                                    id="is_active_toggle"
                                                    {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                                    class="sr-only" @change="switcherToggle = !switcherToggle" />
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
                                    </div>
                                </div>
                            </div>

                            <!-- Change Password Section -->
                            <div
                                class="flex items-center justify-between border-t border-gray-100 pt-6 dark:border-gray-800">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                        {{ __('auth.password') }}
                                    </h4>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('auth.change_password_description') }}
                                    </p>
                                </div>
                                <button type="button" onclick="changePassword({{ $user->id }})"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-brand-500 bg-white px-4 py-2 text-sm font-medium text-brand-600 transition-colors hover:bg-brand-50 dark:border-brand-500 dark:bg-white/[0.03] dark:text-brand-400 dark:hover:bg-brand-500/10">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    {{ __('auth.change_password') }}
                                </button>
                            </div>

                            <!-- Form Actions -->
                            <div
                                class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 dark:border-gray-800 sm:flex-row sm:justify-end">
                                <a href="{{ route('users.index') }}"
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
                                    {{ __('auth.update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <style>
            .swal2-container {
                z-index: 100000 !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .swal2-backdrop-show,
            .swal2-backdrop {
                z-index: 99999 !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                margin: 0 !important;
            }

            body.swal2-height-auto {
                height: 100vh !important;
                overflow: hidden !important;
            }

            .swal2-popup {
                margin: 0 !important;
            }

            .swal2-popup-custom {
                padding: 2rem !important;
                max-width: 500px !important;
            }

            .swal2-html-container-custom {
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            .swal2-title-custom {
                font-size: 1.125rem !important;
                line-height: 1.75rem !important;
            }

            .swal-password-input {
                width: 100% !important;
                margin: 0 !important;
                border: 1px solid rgb(229 231 235) !important;
                border-radius: 0.5rem !important;
                padding: 0.625rem 1rem !important;
                font-size: 0.875rem !important;
                background-color: white !important;
                box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
                transition: all 0.2s !important;
            }

            /* Password styling - ensure browser can render dots properly */
            .swal-password-input[type="password"],
            input[type="password"].swal-password-input {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
                color: #1f2937 !important;
                font-weight: normal !important;
                letter-spacing: 0.025em !important;
            }

            /* Normal text styling for when type="text" */
            .swal-password-input[type="text"],
            input[type="text"].swal-password-input {
                font-weight: 500 !important;
                color: #1f2937 !important;
                letter-spacing: normal !important;
            }

            .swal-password-input::placeholder {
                color: rgb(156 163 175) !important;
                font-weight: 400 !important;
            }

            .swal-password-input:focus {
                border-color: rgb(59 130 246) !important;
                outline: none !important;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1) !important;
            }

            /* Password toggle button styling */
            .swal-password-input+button {
                background: none !important;
                border: none !important;
                padding: 0 !important;
                cursor: pointer !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            /* Force dark text color - override any conflicting styles */
            #swal-password,
            #swal-password-confirmation {
                color: #1f2937 !important;
            }

            #swal-password[type="password"],
            #swal-password-confirmation[type="password"] {
                color: #1f2937 !important;
            }

            #swal-password[type="text"],
            #swal-password-confirmation[type="text"] {
                color: #1f2937 !important;
            }

            /* Dark mode support - only apply if body has dark class */
            body.dark .swal-password-input,
            .dark .swal-password-input {
                border-color: rgb(55 65 81) !important;
                background-color: rgba(255, 255, 255, 0.03) !important;
            }

            body.dark .swal-password-input[type="password"],
            body.dark .swal-password-input[type="text"],
            .dark .swal-password-input[type="password"],
            .dark .swal-password-input[type="text"] {
                color: rgba(255, 255, 255, 0.9) !important;
            }

            body.dark .swal-password-input::placeholder,
            .dark .swal-password-input::placeholder {
                color: rgba(255, 255, 255, 0.3) !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function togglePasswordVisibility(inputId) {
                const input = document.getElementById(inputId);
                const eye = document.getElementById(inputId + '-eye');
                const eyeSlash = document.getElementById(inputId + '-eye-slash');

                if (input && eye && eyeSlash) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        input.style.color = '#1f2937';
                        eye.classList.add('hidden');
                        eyeSlash.classList.remove('hidden');
                    } else {
                        input.type = 'password';
                        input.style.color = '#1f2937';
                        eye.classList.remove('hidden');
                        eyeSlash.classList.add('hidden');
                    }
                }
            }

            function changePassword(userId) {
                Swal.fire({
                    title: '{{ __('auth.change_password') }}',
                    html: `
                        <div class="text-left space-y-4 mt-4">
                            <div class="space-y-2">
                                <label for="swal-password" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    {{ __('auth.password') }}
                                    <span class="text-error-500">*</span>
                                </label>
                                <div class="relative">
                                    <input id="swal-password" type="password"
                                        class="swal-password-input h-11 w-full rounded-lg border border-gray-200 bg-white px-4 pr-12 py-2.5 text-sm shadow-sm transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:placeholder:text-gray-500 dark:focus:border-brand-500"
                                        placeholder="{{ __('auth.enter_password') }}"
                                        style="color: #1f2937 !important;"
                                        required>
                                    <button type="button" onclick="togglePasswordVisibility('swal-password')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                        <svg id="swal-password-eye" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg id="swal-password-eye-slash" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label for="swal-password-confirmation" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    {{ __('auth.confirm_password') }}
                                    <span class="text-error-500">*</span>
                                </label>
                                <div class="relative">
                                    <input id="swal-password-confirmation" type="password"
                                        class="swal-password-input h-11 w-full rounded-lg border border-gray-200 bg-white px-4 pr-12 py-2.5 text-sm shadow-sm transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:placeholder:text-gray-500 dark:focus:border-brand-500"
                                        placeholder="{{ __('auth.confirm_password') }}"
                                        style="color: #1f2937 !important;"
                                        required>
                                    <button type="button" onclick="togglePasswordVisibility('swal-password-confirmation')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                        <svg id="swal-password-confirmation-eye" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg id="swal-password-confirmation-eye-slash" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `,
                    customClass: {
                        popup: 'swal2-popup-custom',
                        htmlContainer: 'swal2-html-container-custom',
                        title: 'swal2-title-custom'
                    },
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '{{ __('auth.update_password') }}',
                    cancelButtonText: '{{ __('auth.cancel') }}',
                    reverseButtons: true,
                    focusConfirm: false,
                    preConfirm: () => {
                        const password = document.getElementById('swal-password').value;
                        const passwordConfirmation = document.getElementById('swal-password-confirmation').value;

                        if (!password) {
                            Swal.showValidationMessage('{{ __('auth.password') }} {{ __('auth.is_required') }}');
                            return false;
                        }

                        if (password.length < 8) {
                            Swal.showValidationMessage('{{ __('auth.password_min') }}');
                            return false;
                        }

                        if (password !== passwordConfirmation) {
                            Swal.showValidationMessage('{{ __('auth.password_confirmed') }}');
                            return false;
                        }

                        return {
                            password: password,
                            password_confirmation: passwordConfirmation
                        };
                    },
                    didOpen: () => {
                        const passwordInput = document.getElementById('swal-password');
                        const confirmInput = document.getElementById('swal-password-confirmation');
                        if (passwordInput) {
                            passwordInput.style.color = '#1f2937';
                            passwordInput.focus();
                        }
                        if (confirmInput) {
                            confirmInput.style.color = '#1f2937';
                        }
                    }
                }).then(async (result) => {
                    if (result.isConfirmed && result.value) {
                        // Show loading state
                        Swal.fire({
                            title: '{{ __('auth.updating') }}...',
                            text: '{{ __('auth.updating_password') }}',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        try {
                            const response = await fetch(`/users/${userId}/password`, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    password: result.value.password,
                                    password_confirmation: result.value.password_confirmation
                                })
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                // Handle validation errors
                                if (response.status === 422) {
                                    throw new Error(data.message || data.errors?.password?.[0] ||
                                        '{{ __('auth.failed_to_update_password') }}');
                                }
                                throw new Error(data.message || '{{ __('auth.failed_to_update_password') }}');
                            }

                            Swal.fire({
                                title: '{{ __('auth.success') }}!',
                                text: data.message || '{{ __('auth.password_updated') }}',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } catch (error) {
                            Swal.fire({
                                title: '{{ __('auth.error') }}!',
                                text: error.message || '{{ __('auth.failed_to_update_password') }}',
                                icon: 'error',
                                confirmButtonColor: '#6b7280'
                            });
                        }
                    }
                });
            }
        </script>
    @endpush
@endsection
