@extends('layouts.app')

@section('title', config('app.name') . ' - Roles')

@section('content')
    <div class="mx-auto max-w-7xl p-4 pb-20 md:p-6 md:pb-6" x-data="rolesDataTable()" x-init="loadData()">
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <!-- Stats Cards -->
            <div class="col-span-12 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Roles Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Roles</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90" x-text="totalEntries">0
                            </p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-500/10">
                            <svg class="fill-brand-500" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M12 2C13.1046 2 14 2.89543 14 4C14 5.10457 13.1046 6 12 6C10.8954 6 10 5.10457 10 4C10 2.89543 10.8954 2 12 2ZM16 8C17.1046 8 18 8.89543 18 10C18 11.1046 17.1046 12 16 12C14.8954 12 14 11.1046 14 10C14 8.89543 14.8954 8 16 8ZM8 8C9.10457 8 10 8.89543 10 10C10 11.1046 9.10457 12 8 12C6.89543 12 6 11.1046 6 10C6 8.89543 6.89543 8 8 8ZM12 14C13.1046 14 14 14.8954 14 16C14 17.1046 13.1046 18 12 18C10.8954 18 10 17.1046 10 16C10 14.8954 10.8954 14 12 14ZM16 14C17.1046 14 18 14.8954 18 16C18 17.1046 17.1046 18 16 18C14.8954 18 14 17.1046 14 16C14 14.8954 14.8954 14 16 14ZM8 14C9.10457 14 10 14.8954 10 16C10 17.1046 9.10457 18 8 18C6.89543 18 6 17.1046 6 16C6 14.8954 6.89543 14 8 14ZM4 20C4 18.8954 4.89543 18 6 18C7.10457 18 8 18.8954 8 20C8 21.1046 7.10457 22 6 22C4.89543 22 4 21.1046 4 20ZM10 20C10 18.8954 10.8954 18 12 18C13.1046 18 14 18.8954 14 20C14 21.1046 13.1046 22 12 22C10.8954 22 10 21.1046 10 20ZM16 20C16 18.8954 16.8954 18 18 18C19.1046 18 20 18.8954 20 20C20 21.1046 19.1046 22 18 22C16.8954 22 16 21.1046 16 20Z"
                                    fill="currentColor" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Roles Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Active Roles</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90" x-text="data.length">0
                            </p>
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

                <!-- Total Permissions Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Permissions</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                                <span x-text="data.reduce((sum, role) => sum + (role.permissions_count || 0), 0)">0</span>
                            </p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500/10">
                            <svg class="fill-blue-500" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9 12L11 14L15 10M7.83594 15.5848C7.83594 15.5848 6.99994 16.4998 8.91694 17.4168C10.8339 18.3338 12.7499 18.3338 14.6669 17.4168C16.5839 16.4998 17.4189 15.5848 17.4189 15.5848M7.83594 8.41516C7.83594 8.41516 6.99994 7.50016 8.91694 6.58316C10.8339 5.66616 12.7499 5.66616 14.6669 6.58316C16.5839 7.50016 17.4189 8.41516 17.4189 8.41516M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Users Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Users</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                                <span x-text="data.reduce((sum, role) => sum + (role.users_count || 0), 0)">0</span>
                            </p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500/10">
                            <svg class="fill-purple-500" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z"
                                    stroke="currentColor" stroke-width="2" />
                                <path d="M12 14C8.13401 14 5 17.134 5 21H19C19 17.134 15.866 14 12 14Z"
                                    stroke="currentColor" stroke-width="2" />
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
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    {{ __('auth.roles') }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage and view all system roles
                                </p>
                            </div>
                            <a href="{{ route('roles.create') }}"
                                class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600">
                                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Add Role
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
                                    <span class="text-gray-500 dark:text-gray-400">Show</span>
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
                                        placeholder="Search..."
                                        class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-11 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 xl:w-[300px]" />
                                </div>
                            </div>

                            <div class="max-w-full overflow-x-auto">
                                <div class="min-w-[1050px]">
                                    <!-- table header start -->
                                    <div class="grid grid-cols-12 border-t border-gray-200 dark:border-gray-800">
                                        <div
                                            class="col-span-1 flex items-center border-r border-gray-200 px-4 py-3 dark:border-gray-800">
                                            <p class="text-theme-xs font-semibold text-gray-700 dark:text-gray-400">
                                                #
                                            </p>
                                        </div>
                                        <div
                                            class="col-span-3 flex items-center border-r border-gray-200 px-4 py-3 dark:border-gray-800">
                                            <div class="flex w-full cursor-pointer items-center justify-between"
                                                @click="sortBy('name')">
                                                <p class="text-theme-xs font-semibold text-gray-700 dark:text-gray-400">
                                                    Role Name
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
                                            class="col-span-2 flex items-center border-r border-gray-200 px-4 py-3 dark:border-gray-800">
                                            <p class="text-theme-xs font-semibold text-gray-700 dark:text-gray-400">
                                                Permissions
                                            </p>
                                        </div>
                                        <div
                                            class="col-span-2 flex items-center border-r border-gray-200 px-4 py-3 dark:border-gray-800">
                                            <p class="text-theme-xs font-semibold text-gray-700 dark:text-gray-400">
                                                Users
                                            </p>
                                        </div>
                                        <div
                                            class="col-span-2 flex items-center border-r border-gray-200 px-4 py-3 dark:border-gray-800">
                                            <div class="flex w-full cursor-pointer items-center justify-between"
                                                @click="sortBy('created_at')">
                                                <p class="text-theme-xs font-semibold text-gray-700 dark:text-gray-400">
                                                    Created At
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
                                            class="col-span-2 flex items-center justify-end px-4 py-3 dark:border-gray-800">
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
                                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Loading...</p>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="!loading && paginatedData.length === 0">
                                        <div class="grid grid-cols-12 border-t border-gray-100 dark:border-gray-800 py-8">
                                            <div class="col-span-12 text-center">
                                                <p class="text-sm text-gray-500 dark:text-gray-400">No roles found</p>
                                                <p class="text-xs text-gray-400 mt-2">Data length: <span
                                                        x-text="data.length"></span>, Loading: <span
                                                        x-text="loading"></span></p>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-for="(role, index) in paginatedData" :key="role.id">
                                        <div class="grid grid-cols-12 border-t border-gray-100 dark:border-gray-800">
                                            <div
                                                class="col-span-1 flex items-center border-r border-gray-100 px-4 py-[17.5px] dark:border-gray-800">
                                                <p class="text-theme-sm text-gray-700 dark:text-gray-400"
                                                    x-text="role.DT_RowIndex || (startEntry + index)">
                                                </p>
                                            </div>
                                            <div
                                                class="col-span-3 flex items-center border-r border-gray-100 px-4 py-[17.5px] dark:border-gray-800">
                                                <span class="inline-flex items-center gap-2">
                                                    <span
                                                        class="text-theme-sm font-semibold text-gray-800 dark:text-white/90"
                                                        x-text="role.name">
                                                    </span>
                                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium"
                                                        :class="{
                                                            'bg-brand-500/10 text-brand-700 dark:bg-brand-500/20 dark:text-brand-400': role
                                                                .name === 'Super Admin',
                                                            'bg-success-500/10 text-success-700 dark:bg-success-500/20 dark:text-success-400': role
                                                                .name === 'Treasurer',
                                                            'bg-blue-500/10 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400': role
                                                                .name === 'Branch Manager',
                                                            'bg-purple-500/10 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400': role
                                                                .name === 'Cashier',
                                                            'bg-gray-500/10 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400':
                                                                !['Super Admin', 'Treasurer', 'Branch Manager',
                                                                    'Cashier'
                                                                ].includes(role.name)
                                                        }"
                                                        x-text="role.name === 'Super Admin' ? 'Admin' : 'Default'">
                                                    </span>
                                                </span>
                                            </div>
                                            <div
                                                class="col-span-2 flex items-center border-r border-gray-100 px-4 py-[17.5px] dark:border-gray-800">
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                                    <svg class="fill-current" width="16" height="16"
                                                        viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M8 7.33333C9.47276 7.33333 10.6667 6.13943 10.6667 4.66667C10.6667 3.19391 9.47276 2 8 2C6.52724 2 5.33333 3.19391 5.33333 4.66667C5.33333 6.13943 6.52724 7.33333 8 7.33333Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M3.33333 14C3.33333 11.4227 5.42267 9.33333 8 9.33333C10.5773 9.33333 12.6667 11.4227 12.6667 14V14.6667H3.33333V14Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                    <span x-text="role.permissions_count || 0"></span>
                                                </span>
                                            </div>
                                            <div
                                                class="col-span-2 flex items-center border-r border-gray-100 px-4 py-[17.5px] dark:border-gray-800">
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full bg-purple-50 px-3 py-1 text-sm font-medium text-purple-700 dark:bg-purple-500/10 dark:text-purple-400">
                                                    <svg class="fill-current" width="16" height="16"
                                                        viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M8 7.33333C9.47276 7.33333 10.6667 6.13943 10.6667 4.66667C10.6667 3.19391 9.47276 2 8 2C6.52724 2 5.33333 3.19391 5.33333 4.66667C5.33333 6.13943 6.52724 7.33333 8 7.33333Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M3.33333 14C3.33333 11.4227 5.42267 9.33333 8 9.33333C10.5773 9.33333 12.6667 11.4227 12.6667 14V14.6667H3.33333V14Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                    <span x-text="role.users_count || 0"></span>
                                                </span>
                                            </div>
                                            <div
                                                class="col-span-2 flex items-center border-r border-gray-100 px-4 py-[17.5px] dark:border-gray-800">
                                                <span
                                                    class="inline-flex items-center gap-1.5 text-theme-sm text-gray-700 dark:text-gray-400">
                                                    <svg class="fill-current" width="16" height="16"
                                                        viewBox="0 0 16 16" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M8 1.33333C4.318 1.33333 1.33333 4.318 1.33333 8C1.33333 11.682 4.318 14.6667 8 14.6667C11.682 14.6667 14.6667 11.682 14.6667 8C14.6667 4.318 11.682 1.33333 8 1.33333ZM8 13.3333C5.05467 13.3333 2.66667 10.9453 2.66667 8C2.66667 5.05467 5.05467 2.66667 8 2.66667C10.9453 2.66667 13.3333 5.05467 13.3333 8C13.3333 10.9453 10.9453 13.3333 8 13.3333ZM8.33333 4.66667H7.33333V8.33333H10.6667V7.33333H8.33333V4.66667Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                    <span x-text="role.created_at"></span>
                                                </span>
                                            </div>
                                            <div class="col-span-2 flex items-center justify-end px-4 py-[17.5px]">
                                                <div class="flex items-center gap-2" x-html="role.actions">
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
                                            Previous
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
                                        Showing <span x-text="startEntry"></span> to
                                        <span x-text="endEntry"></span> of
                                        <span x-text="totalEntries"></span> entries
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
            /* Ensure SweetAlert2 backdrop covers entire screen including sidebar and header */
            /* Header has z-99999, sidebar has z-9999, so we need higher z-index for SweetAlert */
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

            /* Ensure backdrop covers full viewport */
            body.swal2-height-auto {
                height: 100vh !important;
                overflow: hidden !important;
            }

            /* Ensure modal is centered on full screen */
            .swal2-popup {
                margin: 0 !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Edit Role function
            function editRole(roleId) {
                // Prevent editing Super Admin (role id 1)
                if (roleId === 1) {
                    Swal.fire({
                        title: 'Cannot Edit',
                        text: 'The Super Admin role cannot be edited.',
                        icon: 'error',
                        confirmButtonColor: '#6b7280',
                        customClass: {
                            container: 'swal2-container-custom'
                        },
                        backdrop: true,
                        allowOutsideClick: true
                    });
                    return;
                }
                window.location.href = `/roles/${roleId}/edit`;
            }

            // Delete Role function with SweetAlert
            function deleteRole(roleId, roleName) {
                // Prevent deletion of Super Admin (role id 1)
                if (roleId === 1) {
                    Swal.fire({
                        title: 'Cannot Delete',
                        text: 'The Super Admin role cannot be deleted.',
                        icon: 'error',
                        confirmButtonColor: '#6b7280',
                        customClass: {
                            container: 'swal2-container-custom'
                        },
                        backdrop: true,
                        allowOutsideClick: true
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete the role "${roleName}"? This action cannot be undone!`,
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
                            const response = await fetch(`/roles/${roleId}`, {
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
                                    message: 'Failed to delete role'
                                }));
                                throw new Error(errorData.message || 'Failed to delete role');
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
                            text: `The role "${roleName}" has been deleted.`,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Reload table data
                            const table = document.querySelector('[x-data*="rolesDataTable"]');
                            if (table && table._x_dataStack && table._x_dataStack[0]) {
                                table._x_dataStack[0].loadData();
                            } else {
                                window.location.reload();
                            }
                        });
                    }
                });
            }

            // Ensure Alpine is available
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Alpine === 'undefined') {
                    console.error('Alpine.js is not loaded!');
                } else {
                    console.log('Alpine.js is available');
                }
            });

            function rolesDataTable() {
                return {
                    search: "",
                    sortColumn: "name",
                    sortDirection: "asc",
                    currentPage: 1,
                    perPage: 10,
                    data: [],
                    loading: false,
                    totalRecords: 0,

                    init() {
                        console.log('Alpine.js rolesDataTable initialized');
                        // Don't load data here since x-init="loadData()" is on parent
                    },

                    async loadData() {
                        console.log('Loading data...', {
                            currentPage: this.currentPage,
                            perPage: this.perPage,
                            search: this.search
                        });
                        this.loading = true;
                        try {
                            // Build query parameters for Yajra DataTables format
                            const params = new URLSearchParams();
                            params.append('draw', '1');
                            params.append('start', String((this.currentPage - 1) * this.perPage));
                            params.append('length', String(this.perPage));
                            params.append('search[value]', this.search || '');
                            // Temporarily disable ordering to avoid column index errors
                            // Will re-enable once DataTables column mapping is fixed
                            // if (this.sortColumn === 'name') {
                            //     params.append('order[0][column]', '1');
                            //     params.append('order[0][dir]', this.sortDirection);
                            // } else if (this.sortColumn === 'created_at') {
                            //     params.append('order[0][column]', '4');
                            //     params.append('order[0][dir]', this.sortDirection);
                            // }

                            const url = `{{ route('roles.data') }}?${params.toString()}`;
                            console.log('Fetching from:', url);

                            const response = await fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                                credentials: 'same-origin'
                            });

                            console.log('Response status:', response.status, response.statusText);

                            if (!response.ok) {
                                const errorText = await response.text();
                                console.error('HTTP error response:', errorText);
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }

                            const contentType = response.headers.get('content-type');
                            console.log('Content-Type:', contentType);

                            if (!contentType || !contentType.includes('application/json')) {
                                const text = await response.text();
                                console.error('Non-JSON response:', text.substring(0, 500));
                                throw new Error('Response is not JSON. Might be a redirect to login page.');
                            }

                            const result = await response.json();
                            console.log('DataTables response:', result);
                            console.log('Data array:', result.data);
                            console.log('Data is array?', Array.isArray(result.data));
                            console.log('Data length:', result.data?.length);

                            if (result.data && Array.isArray(result.data)) {
                                console.log('Processing data array...');
                                this.data = result.data.map((item, index) => {
                                    console.log('Processing item:', item);
                                    return {
                                        id: item.id,
                                        DT_RowIndex: item.DT_RowIndex || ((this.currentPage - 1) * this.perPage +
                                            index + 1),
                                        name: item.name,
                                        permissions_count: item.permissions_count ?? 0,
                                        users_count: item.users_count ?? 0,
                                        created_at: item.created_at,
                                        actions: item.actions || ''
                                    };
                                });
                                this.totalRecords = result.recordsFiltered || result.recordsTotal || 0;
                                console.log('Final data:', this.data);
                                console.log('Total records:', this.totalRecords);
                            } else {
                                console.warn('Unexpected response format:', result);
                                this.data = [];
                                this.totalRecords = 0;
                            }
                        } catch (error) {
                            console.error('Error loading data:', error);
                            console.error('Error stack:', error.stack);
                            this.data = [];
                            this.totalRecords = 0;
                        } finally {
                            this.loading = false;
                            console.log('Loading complete. Data length:', this.data.length);
                        }
                    },

                    getColumnIndex(column) {
                        // Map to DataTables column indices
                        // Note: DataTables uses 0-based indexing
                        // Actual DB columns: id, name, guard_name, created_at, updated_at
                        // Added columns: permissions_count, users_count, actions
                        // So the order is: id(0), name(1), guard_name(2), created_at(3), updated_at(4), permissions_count(5), users_count(6), actions(7)
                        // But we only want to allow sorting on name and created_at
                        const columnMap = {
                            'name': 1, // name column
                            'created_at': 3 // created_at column
                        };
                        return columnMap[column] || 1; // default to name
                    },

                    get filteredData() {
                        return this.data;
                    },

                    get paginatedData() {
                        // Data is already paginated from server
                        console.log('paginatedData getter called, data length:', this.data.length);
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
