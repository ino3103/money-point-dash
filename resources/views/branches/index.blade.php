@extends('layouts.app')

@section('title', config('app.name') . ' - Branches')

@section('content')
    <div class="mx-auto max-w-7xl p-4 pb-20 md:p-6 md:pb-6" x-data="branchesDataTable()" x-init="loadData()">
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <!-- Stats Cards -->
            <div class="col-span-12 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
                <!-- Total Branches Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('auth.total_branches') }}</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90" x-text="totalEntries">0
                            </p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-500/10">
                            <svg class="fill-brand-500" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19 21H5C4.44772 21 4 20.5523 4 20V11L1 11L11.3273 1.6115C11.7087 1.26475 12.2913 1.26475 12.6727 1.6115L23 11L20 11V20C20 20.5523 19.5523 21 19 21ZM6 19H18V9.15745L12 3.7029L6 9.15745V19ZM8 15V17H16V15H8Z"
                                    fill="currentColor" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Branches Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('auth.active_branches') }}</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90"
                                x-text="data.filter(b => b.is_active).length">0</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success-500/10">
                            <svg class="fill-success-500" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Inactive Branches Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('auth.inactive_branches') }}</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90"
                                x-text="data.filter(b => !b.is_active).length">0</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-500/10">
                            <svg class="fill-gray-500" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                    stroke="currentColor" stroke-width="2" />
                                <path d="M8 12H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Branches with Code Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">With Code</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90"
                                x-text="data.filter(b => b.code && b.code !== '-').length">0</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500/10">
                            <svg class="fill-blue-500" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 20L11 4M13 20L17 4M6 9H20M4 15H18" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="col-span-12">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-5 py-4 sm:px-6 sm:py-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ __('auth.branches') }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('auth.manage_branches') }}
                                </p>
                            </div>
                            <a href="{{ route('branches.create') }}"
                                class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600">
                                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Add Branch
                            </a>
                        </div>
                    </div>
                    <div class="px-5 sm:px-6">
                        @if (session('success'))
                            <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                x-init="setTimeout(() => show = false, 5000)"
                                class="mt-4 rounded-lg border border-success-200 bg-success-50 p-4 shadow-sm dark:border-success-800 dark:bg-success-500/10">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-success-600 dark:text-success-400" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-semibold text-success-800 dark:text-success-300">Success!
                                        </h3>
                                        <p class="mt-1 text-sm text-success-700 dark:text-success-400">
                                            {{ session('success') }}
                                        </p>
                                    </div>
                                    <button @click="show = false" type="button"
                                        class="flex-shrink-0 rounded-md p-1.5 text-success-500 hover:bg-success-100 focus:outline-none focus:ring-2 focus:ring-success-500 focus:ring-offset-2 focus:ring-offset-success-50 dark:hover:bg-success-500/20 dark:focus:ring-offset-success-500/10">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif
                        @if (session('error'))
                            <div x-data="{ show: true }" x-show="show"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                x-init="setTimeout(() => show = false, 5000)"
                                class="mt-4 rounded-lg border border-error-200 bg-error-50 p-4 shadow-sm dark:border-error-800 dark:bg-error-500/10">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-error-600 dark:text-error-400" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-semibold text-error-800 dark:text-error-300">Error</h3>
                                        <p class="mt-1 text-sm text-error-700 dark:text-error-400">
                                            {{ session('error') }}
                                        </p>
                                    </div>
                                    <button @click="show = false" type="button"
                                        class="flex-shrink-0 rounded-md p-1.5 text-error-500 hover:bg-error-100 focus:outline-none focus:ring-2 focus:ring-error-500 focus:ring-offset-2 focus:ring-offset-error-50 dark:hover:bg-error-500/20 dark:focus:ring-offset-error-500/10">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
                        <!-- ====== DataTable Start ====== -->
                        <div
                            class="overflow-hidden rounded-xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
                            <div class="mb-4 flex flex-col gap-2 px-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('auth.show') }}</span>
                                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                                        <select
                                            class="dark:bg-dark-900 h-9 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none py-2 pl-3 pr-8 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                            :class="isOptionSelected && 'text-gray-500 dark:text-gray-400'"
                                            @click="isOptionSelected = true"
                                            @change="perPage = parseInt($event.target.value); loadData()">
                                            <option value="10"
                                                class="text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                                                10
                                            </option>
                                            <option value="25"
                                                class="text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                                                25
                                            </option>
                                            <option value="50"
                                                class="text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                                                50
                                            </option>
                                            <option value="100"
                                                class="text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                                                100
                                            </option>
                                        </select>
                                        <span
                                            class="absolute right-2 top-1/2 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                            <svg class="stroke-current" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3.8335 5.9165L8.00016 10.0832L12.1668 5.9165" stroke=""
                                                    stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </div>
                                    <span class="text-gray-500 dark:text-gray-400">entries</span>

                                    <!-- Status Filter -->
                                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                                        <select
                                            class="dark:bg-dark-900 h-9 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none py-2 pl-3 pr-8 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                            @change="statusFilter = $event.target.value; loadData()">
                                            <option value="">All Status</option>
                                            <option value="active">Active Only</option>
                                            <option value="inactive">Inactive Only</option>
                                        </select>
                                        <span
                                            class="absolute right-2 top-1/2 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                            <svg class="stroke-current" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3.8335 5.9165L8.00016 10.0832L12.1668 5.9165" stroke=""
                                                    stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <div class="relative">
                                    <button
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"
                                                fill="" />
                                        </svg>
                                    </button>

                                    <input type="text" x-model="search" @input.debounce.300ms="loadData()"
                                        placeholder="{{ __('auth.search') }}..."
                                        class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-11 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 xl:w-[300px]" />
                                </div>
                            </div>

                            <div class="max-w-full overflow-x-auto">
                                <div class="min-w-[1200px]">
                                    <!-- table header start -->
                                    <div class="grid grid-cols-12 border-t border-gray-200 dark:border-gray-800">
                                        <div
                                            class="col-span-1 flex items-center border-r border-gray-200 px-4 py-3 dark:border-gray-800">
                                            <p class="text-theme-xs font-semibold text-gray-700 dark:text-gray-400">#</p>
                                        </div>
                                        <div
                                            class="col-span-2 flex items-center border-r border-gray-200 px-4 py-3 dark:border-gray-800">
                                            <div class="flex w-full cursor-pointer items-center justify-between"
                                                @click="sortBy('name')">
                                                <p class="text-theme-xs font-semibold text-gray-700 dark:text-gray-400">
                                                    {{ __('auth.branch_name') }}
                                                </p>
                                                <span class="flex flex-col gap-0.5">
                                                    <svg class="fill-gray-300 dark:fill-gray-700" width="8"
                                                        height="5" viewBox="0 0 8 5" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M4.40962 0.585167C4.21057 0.300808 3.78943 0.300807 3.59038 0.585166L1.05071 4.21327C0.81874 4.54466 1.05582 5 1.46033 5H6.53967C6.94418 5 7.18126 4.54466 6.94929 4.21327L4.40962 0.585167Z"
                                                            fill="" />
                                                    </svg>
                                                    <svg class="fill-gray-300 dark:fill-gray-700" width="8"
                                                        height="5" viewBox="0 0 8 5" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M4.40962 4.41483C4.21057 4.69919 3.78943 4.69919 3.59038 4.41483L1.05071 0.786732C0.81874 0.455343 1.05582 0 1.46033 0H6.53967C6.94418 0 7.18126 0.455342 6.94929 0.786731L4.40962 4.41483Z"
                                                            fill="" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                        <div
                                            class="col-span-1 flex items-center border-r border-gray-200 px-4 py-3 dark:border-gray-800">
                                            <p class="text-theme-xs font-semibold text-gray-700 dark:text-gray-400">Code
                                            </p>
                                        </div>
                                        <div
                                            class="col-span-2 flex items-center border-r border-gray-200 px-4 py-3 dark:border-gray-800">
                                            <p class="text-theme-xs font-semibold text-gray-700 dark:text-gray-400">Email
                                            </p>
                                        </div>
                                        <div
                                            class="col-span-2 flex items-center border-r border-gray-200 px-4 py-3 dark:border-gray-800">
                                            <p class="text-theme-xs font-semibold text-gray-700 dark:text-gray-400">Phone
                                            </p>
                                        </div>
                                        <div
                                            class="col-span-2 flex items-center border-r border-gray-200 px-4 py-3 dark:border-gray-800">
                                            <p class="text-theme-xs font-semibold text-gray-700 dark:text-gray-400">Address
                                            </p>
                                        </div>
                                        <div
                                            class="col-span-1 flex items-center border-r border-gray-200 px-4 py-3 dark:border-gray-800">
                                            <p class="text-theme-xs font-semibold text-gray-700 dark:text-gray-400">Status
                                            </p>
                                        </div>
                                        <div
                                            class="col-span-1 flex items-center justify-end px-4 py-3 dark:border-gray-800">
                                            <p class="text-theme-xs font-semibold text-gray-700 dark:text-gray-400">
                                                Actions
                                            </p>
                                        </div>
                                    </div>
                                    <!-- table header end -->

                                    <!-- table body start -->
                                    <template x-if="loading">
                                        <div class="grid grid-cols-12 border-t border-gray-100 dark:border-gray-800 py-8">
                                            <div class="col-span-12 text-center">
                                                <div
                                                    class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent">
                                                </div>
                                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ __('auth.loading') }}...</p>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="!loading && paginatedData.length === 0">
                                        <div class="grid grid-cols-12 border-t border-gray-100 dark:border-gray-800 py-12">
                                            <div class="col-span-12 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                                        <svg class="h-8 w-8 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 21H5C4.44772 21 4 20.5523 4 20V11L1 11L11.3273 1.6115C11.7087 1.26475 12.2913 1.26475 12.6727 1.6115L23 11L20 11V20C20 20.5523 19.5523 21 19 21ZM6 19H18V9.15745L12 3.7029L6 9.15745V19ZM8 15V17H16V15H8Z" />
                                                        </svg>
                                                    </div>
                                                    <h3 class="mt-4 text-base font-semibold text-gray-900 dark:text-white">
                                                        No branches found
                                                    </h3>
                                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                        <span x-show="search || statusFilter">
                                                            Try adjusting your search or filter criteria.
                                                        </span>
                                                        <span x-show="!search && !statusFilter">
                                                            {{ __('auth.create_first_branch') }}
                                                        </span>
                                                    </p>
                                                    <div class="mt-6" x-show="!search && !statusFilter">
                                                        <a href="{{ route('branches.create') }}"
                                                            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                            Add Your First Branch
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-for="(branch, index) in paginatedData" :key="branch.id">
                                        <div class="grid grid-cols-12 border-t border-gray-100 dark:border-gray-800">
                                            <div
                                                class="col-span-1 flex items-center border-r border-gray-100 px-4 py-[17.5px] dark:border-gray-800">
                                                <p class="text-theme-sm text-gray-700 dark:text-gray-400"
                                                    x-text="branch.DT_RowIndex || (startEntry + index)">
                                                </p>
                                            </div>
                                            <div
                                                class="col-span-2 flex items-center border-r border-gray-100 px-4 py-[17.5px] dark:border-gray-800">
                                                <span class="text-theme-sm font-semibold text-gray-800 dark:text-white/90"
                                                    x-text="branch.name">
                                                </span>
                                            </div>
                                            <div
                                                class="col-span-1 flex items-center border-r border-gray-100 px-4 py-[17.5px] dark:border-gray-800">
                                                <span class="text-theme-sm text-gray-700 dark:text-gray-400"
                                                    x-text="branch.code || '-'">
                                                </span>
                                            </div>
                                            <div
                                                class="col-span-2 flex items-center border-r border-gray-100 px-4 py-[17.5px] dark:border-gray-800">
                                                <span class="text-theme-sm text-gray-700 dark:text-gray-400"
                                                    x-text="branch.email || '-'">
                                                </span>
                                            </div>
                                            <div
                                                class="col-span-2 flex items-center border-r border-gray-100 px-4 py-[17.5px] dark:border-gray-800">
                                                <span class="text-theme-sm text-gray-700 dark:text-gray-400"
                                                    x-text="branch.phone || '-'">
                                                </span>
                                            </div>
                                            <div
                                                class="col-span-2 flex items-center border-r border-gray-100 px-4 py-[17.5px] dark:border-gray-800">
                                                <span
                                                    class="text-theme-sm text-gray-700 dark:text-gray-400 truncate block max-w-full"
                                                    :title="branch.address || '-'" x-text="branch.address || '-'">
                                                </span>
                                            </div>
                                            <div
                                                class="col-span-1 flex items-center border-r border-gray-100 px-4 py-[17.5px] dark:border-gray-800">
                                                <div x-html="branch.status"></div>
                                            </div>
                                            <div class="col-span-1 flex items-center justify-end px-4 py-[17.5px]">
                                                <div class="flex items-center gap-2" x-html="branch.actions">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <!-- table body end -->
                                </div>
                            </div>

                            <!-- Pagination Controls -->
                            <div class="border-t border-gray-100 py-4 pl-[18px] pr-4 dark:border-gray-800">
                                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between">
                                    <div class="flex items-center justify-center gap-0.5 pb-4 xl:justify-normal xl:pt-0">
                                        <button @click="prevPage()"
                                            class="mr-2.5 flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-gray-700 shadow-theme-xs hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
                                            :disabled="currentPage === 1">
                                            {{ __('auth.previous') }}
                                        </button>

                                        <button @click="goToPage(1)"
                                            :class="currentPage === 1 ? 'bg-blue-500/[0.08] text-brand-500' :
                                                'text-gray-700 dark:text-gray-400'"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium hover:bg-blue-500/[0.08] hover:text-brand-500 dark:hover:text-brand-500">
                                            1
                                        </button>

                                        <template x-if="currentPage > 3">
                                            <span
                                                class="flex h-10 w-10 items-center justify-center rounded-lg hover:bg-blue-500/[0.08] hover:text-brand-500 dark:hover:text-brand-500">...</span>
                                        </template>

                                        <template x-for="page in pagesAroundCurrent" :key="page">
                                            <button @click="goToPage(page)"
                                                :class="currentPage === page ? 'bg-blue-500/[0.08] text-brand-500' :
                                                    'text-gray-700 dark:text-gray-400'"
                                                class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium hover:bg-blue-500/[0.08] hover:text-brand-500 dark:hover:text-brand-500">
                                                <span x-text="page"></span>
                                            </button>
                                        </template>

                                        <template x-if="currentPage < totalPages - 2">
                                            <span
                                                class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium text-gray-700 hover:bg-blue-500/[0.08] hover:text-brand-500 dark:text-gray-400 dark:hover:text-brand-500">...</span>
                                        </template>

                                        <button @click="nextPage()"
                                            class="ml-2.5 flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-gray-700 shadow-theme-xs hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
                                            :disabled="currentPage === totalPages">
                                            Next
                                        </button>
                                    </div>

                                    <p
                                        class="border-t border-gray-100 pt-3 text-center text-sm font-medium text-gray-500 dark:border-gray-800 dark:text-gray-400 xl:border-t-0 xl:pt-0 xl:text-left">
                                        {{ __('auth.showing') }} <span x-text="startEntry"></span> {{ __('auth.to') }}
                                        <span x-text="endEntry"></span> {{ __('auth.of') }}
                                        <span x-text="totalEntries"></span> {{ __('auth.entries') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- ====== DataTable End ====== -->
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
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function editBranch(branchId) {
                window.location.href = `/branches/${branchId}/edit`;
            }

            function deleteBranch(branchId, branchName) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete the branch "${branchName}"? This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    preConfirm: async () => {
                        try {
                            const response = await fetch(`/branches/${branchId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            });

                            if (!response.ok) {
                                const errorData = await response.json().catch(() => ({
                                    message: 'Failed to delete branch'
                                }));
                                throw new Error(errorData.message || 'Failed to delete branch');
                            }

                            return response.json();
                        } catch (error) {
                            Swal.showValidationMessage(`Request failed: ${error.message}`);
                        }
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: `The branch "${branchName}" has been deleted.`,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            const table = document.querySelector('[x-data*="branchesDataTable"]');
                            if (table && table._x_dataStack && table._x_dataStack[0]) {
                                table._x_dataStack[0].loadData();
                            } else {
                                window.location.reload();
                            }
                        });
                    }
                });
            }

            function branchesDataTable() {
                return {
                    search: "",
                    statusFilter: "",
                    sortColumn: "name",
                    sortDirection: "asc",
                    currentPage: 1,
                    perPage: 10,
                    data: [],
                    loading: false,
                    totalRecords: 0,

                    init() {
                        console.log('Alpine.js branchesDataTable initialized');
                    },

                    async loadData() {
                        this.loading = true;
                        try {
                            const params = new URLSearchParams();
                            params.append('draw', '1');
                            params.append('start', String((this.currentPage - 1) * this.perPage));
                            params.append('length', String(this.perPage));
                            // Combine search and status filter
                            let searchValue = this.search || '';
                            if (this.statusFilter) {
                                searchValue = (searchValue ? searchValue + ' ' : '') + (this.statusFilter === 'active' ?
                                    'active' : 'inactive');
                            }
                            params.append('search[value]', searchValue);

                            const url = `{{ route('branches.data') }}?${params.toString()}`;

                            const response = await fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                                credentials: 'same-origin'
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }

                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                throw new Error('Response is not JSON.');
                            }

                            const result = await response.json();

                            if (result.data && Array.isArray(result.data)) {
                                this.data = result.data.map((item, index) => {
                                    return {
                                        id: item.id,
                                        DT_RowIndex: item.DT_RowIndex || ((this.currentPage - 1) * this.perPage +
                                            index + 1),
                                        name: item.name,
                                        code: item.code,
                                        email: item.email,
                                        phone: item.phone,
                                        address: item.address || '',
                                        is_active: item.is_active,
                                        status: item.status || '',
                                        actions: item.actions || ''
                                    };
                                });
                                this.totalRecords = result.recordsFiltered || result.recordsTotal || 0;
                            } else {
                                this.data = [];
                                this.totalRecords = 0;
                            }
                        } catch (error) {
                            console.error('Error loading data:', error);
                            this.data = [];
                            this.totalRecords = 0;
                        } finally {
                            this.loading = false;
                        }
                    },

                    get paginatedData() {
                        return this.data;
                    },

                    get totalEntries() {
                        return this.totalRecords;
                    },

                    get startEntry() {
                        return (this.currentPage - 1) * this.perPage + 1;
                    },

                    get endEntry() {
                        const end = this.currentPage * this.perPage;
                        return end > this.totalEntries ? this.totalEntries : end;
                    },

                    get totalPages() {
                        return Math.ceil(this.totalEntries / this.perPage);
                    },

                    get pagesAroundCurrent() {
                        let pages = [];
                        const startPage = Math.max(2, this.currentPage - 2);
                        const endPage = Math.min(this.totalPages - 1, this.currentPage + 2);

                        for (let i = startPage; i <= endPage; i++) {
                            pages.push(i);
                        }
                        return pages;
                    },

                    goToPage(page) {
                        if (page >= 1 && page <= this.totalPages) {
                            this.currentPage = page;
                            this.loadData();
                        }
                    },

                    nextPage() {
                        if (this.currentPage < this.totalPages) {
                            this.currentPage++;
                            this.loadData();
                        }
                    },

                    prevPage() {
                        if (this.currentPage > 1) {
                            this.currentPage--;
                            this.loadData();
                        }
                    },

                    sortBy(column) {
                        if (this.sortColumn === column) {
                            this.sortDirection = this.sortDirection === "asc" ? "desc" : "asc";
                        } else {
                            this.sortDirection = "asc";
                            this.sortColumn = column;
                        }
                        this.currentPage = 1;
                        this.loadData();
                    },
                };
            }
        </script>
    @endpush
@endsection
