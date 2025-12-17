@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 pb-20 md:p-6 md:pb-6">
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <div class="col-span-12">
                <!-- Metric Group Two -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 xl:grid-cols-4">
                    <!-- Metric Item Start -->
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">
                            Unique Visitors
                        </p>

                        <div class="mt-3 flex items-end justify-between">
                            <div>
                                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                                    24.7K
                                </h4>
                            </div>

                            <div class="flex items-center gap-1">
                                <span
                                    class="flex items-center gap-1 rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                    +20%
                                </span>

                                <span class="text-theme-xs text-gray-500 dark:text-gray-400">
                                    Vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Metric Item End -->

                    <!-- Metric Item Start -->
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">
                            Total Pageviews
                        </p>

                        <div class="mt-3 flex items-end justify-between">
                            <div>
                                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                                    55.9K
                                </h4>
                            </div>

                            <div class="flex items-center gap-1">
                                <span
                                    class="flex items-center gap-1 rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                    +4%
                                </span>

                                <span class="text-theme-xs text-gray-500 dark:text-gray-400">
                                    Vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Metric Item End -->

                    <!-- Metric Item Start -->
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">Bounce Rate</p>

                        <div class="mt-3 flex items-end justify-between">
                            <div>
                                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">54%</h4>
                            </div>

                            <div class="flex items-center gap-1">
                                <span
                                    class="flex items-center gap-1 rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                                    -1.59%
                                </span>

                                <span class="text-theme-xs text-gray-500 dark:text-gray-400">
                                    Vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Metric Item End -->

                    <!-- Metric Item Start -->
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">Visit Duration</p>

                        <div class="mt-3 flex items-end justify-between">
                            <div>
                                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                                    2m 56s
                                </h4>
                            </div>

                            <div class="flex items-center gap-1">
                                <span
                                    class="flex items-center gap-1 rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                    +7%
                                </span>

                                <span class="text-theme-xs text-gray-500 dark:text-gray-400">
                                    Vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Metric Item End -->
                </div>
                <!-- Metric Group Two -->
            </div>

            <div class="col-span-12">
                <!-- ====== Chart Four Start -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
                    <div class="flex flex-wrap items-start justify-between gap-5">
                        <div>
                            <h3 class="mb-1 text-lg font-semibold text-gray-800 dark:text-white/90">
                                Analytics
                            </h3>
                            <span class="block text-theme-sm text-gray-500 dark:text-gray-400">
                                Visitor analytics of last 30 days
                            </span>
                        </div>

                        <div x-data="{ selected: 'optionOne' }"
                            class="flex items-center gap-0.5 rounded-lg bg-gray-100 p-0.5 dark:bg-gray-900">
                            <button @click="selected = 'optionOne'"
                                :class="selected === 'optionOne' ?
                                    'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' :
                                    'text-gray-500 dark:text-gray-400'"
                                class=":hover:text-white rounded-md px-3 py-2 text-theme-sm font-medium hover:text-gray-900">
                                12 months
                            </button>

                            <button @click="selected = 'optionTwo'"
                                :class="selected === 'optionTwo' ?
                                    'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' :
                                    'text-gray-500 dark:text-gray-400'"
                                class="hover:text-gray-900dark:hover:text-white rounded-md px-3 py-2 text-theme-sm font-medium">
                                30 days
                            </button>

                            <button @click="selected = 'optionThree'"
                                :class="selected === 'optionThree' ?
                                    'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' :
                                    'text-gray-500 dark:text-gray-400'"
                                class="rounded-md px-3 py-2 text-theme-sm font-medium hover:text-gray-900 dark:hover:text-white">
                                7 days
                            </button>

                            <button @click="selected = 'optionFour'"
                                :class="selected === 'optionFour' ?
                                    'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' :
                                    'text-gray-500 dark:text-gray-400'"
                                class="rounded-md px-3 py-2 text-theme-sm font-medium hover:text-gray-900 dark:hover:text-white">
                                24 hours
                            </button>
                        </div>
                    </div>
                    <div class="custom-scrollbar max-w-full overflow-x-auto">
                        <div id="chartFour" class="-ml-5 min-w-[1300px] pl-2"></div>
                    </div>
                </div>
                <!-- ====== Chart Four End -->
            </div>

            <div class="col-span-12 xl:col-span-7">
                <!-- ====== Top Card Group Start -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Card item -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                        <div class="flex items-start justify-between">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Top Channels
                            </h3>

                            <div x-data="{ openDropDown: false }" class="relative">
                                <button @click="openDropDown = !openDropDown"
                                    :class="openDropDown ? 'text-gray-700 dark:text-white' :
                                        'text-gray-400 hover:text-gray-700 dark:hover:text-white'">
                                    <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                            fill="" />
                                    </svg>
                                </button>
                                <div x-show="openDropDown" @click.outside="openDropDown = false"
                                    class="absolute right-0 top-full z-40 w-40 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark">
                                    <button
                                        class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                        View More
                                    </button>
                                    <button
                                        class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="my-6">
                            <div
                                class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                                <span class="text-theme-xs text-gray-400"> Source </span>
                                <span class="text-right text-theme-xs text-gray-400"> Visitors </span>
                            </div>

                            <div
                                class="flex items-center justify-between border-b border-gray-100 py-3 dark:border-gray-800">
                                <span class="text-theme-sm text-gray-500 dark:text-gray-400">
                                    Google
                                </span>
                                <span class="text-right text-theme-sm text-gray-500 dark:text-gray-400">
                                    4.7K
                                </span>
                            </div>

                            <div
                                class="flex items-center justify-between border-b border-gray-100 py-3 dark:border-gray-800">
                                <span class="text-theme-sm text-gray-500 dark:text-gray-400">
                                    Facebook
                                </span>
                                <span class="text-right text-theme-sm text-gray-500 dark:text-gray-400">
                                    3.4K
                                </span>
                            </div>

                            <div
                                class="flex items-center justify-between border-b border-gray-100 py-3 dark:border-gray-800">
                                <span class="text-theme-sm text-gray-500 dark:text-gray-400">
                                    Threads
                                </span>
                                <span class="text-right text-theme-sm text-gray-500 dark:text-gray-400">
                                    2.9K
                                </span>
                            </div>

                            <div
                                class="flex items-center justify-between border-b border-gray-100 py-3 dark:border-gray-800">
                                <span class="text-theme-sm text-gray-500 dark:text-gray-400">
                                    Google
                                </span>
                                <span class="text-right text-theme-sm text-gray-500 dark:text-gray-400">
                                    1.5K
                                </span>
                            </div>
                        </div>

                        <a href="#"
                            class="flex justify-center gap-2 rounded-lg border border-gray-300 bg-white p-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Channels Report
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
                                    fill="" />
                            </svg>
                        </a>
                    </div>

                    <!-- Card item -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                        <div class="flex items-start justify-between">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Top Pages
                            </h3>

                            <div x-data="{ openDropDown: false }" class="relative">
                                <button @click="openDropDown = !openDropDown"
                                    :class="openDropDown ? 'text-gray-700 dark:text-white' :
                                        'text-gray-400 hover:text-gray-700 dark:hover:text-white'">
                                    <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                            fill="" />
                                    </svg>
                                </button>
                                <div x-show="openDropDown" @click.outside="openDropDown = false"
                                    class="absolute right-0 top-full z-40 w-40 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark">
                                    <button
                                        class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                        View More
                                    </button>
                                    <button
                                        class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="my-6">
                            <div
                                class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                                <span class="text-theme-xs text-gray-400"> Source </span>
                                <span class="text-right text-theme-xs text-gray-400"> Pageview </span>
                            </div>

                            <div
                                class="flex items-center justify-between border-b border-gray-100 py-3 dark:border-gray-800">
                                <span class="text-theme-sm text-gray-500 dark:text-gray-400">
                                    tailadmin.com
                                </span>
                                <span class="text-right text-theme-sm text-gray-500 dark:text-gray-400">
                                    4.7K
                                </span>
                            </div>

                            <div
                                class="flex items-center justify-between border-b border-gray-100 py-3 dark:border-gray-800">
                                <span class="text-theme-sm text-gray-500 dark:text-gray-400">
                                    preview.tailadmin.com
                                </span>
                                <span class="text-right text-theme-sm text-gray-500 dark:text-gray-400">
                                    3.4K
                                </span>
                            </div>

                            <div
                                class="flex items-center justify-between border-b border-gray-100 py-3 dark:border-gray-800">
                                <span class="text-theme-sm text-gray-500 dark:text-gray-400">
                                    docs.tailadmin.com
                                </span>
                                <span class="text-right text-theme-sm text-gray-500 dark:text-gray-400">
                                    2.9K
                                </span>
                            </div>

                            <div
                                class="flex items-center justify-between border-b border-gray-100 py-3 dark:border-gray-800">
                                <span class="text-theme-sm text-gray-500 dark:text-gray-400">
                                    tailadmin.com/componetns
                                </span>
                                <span class="text-right text-theme-sm text-gray-500 dark:text-gray-400">
                                    1.5K
                                </span>
                            </div>
                        </div>

                        <a href="#"
                            class="flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white p-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            Channels Report
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
                                    fill="" />
                            </svg>
                        </a>
                    </div>
                </div>
                <!-- ====== Top Card Group End -->
            </div>

            <div class="col-span-12 xl:col-span-5">
                <!-- ====== Chart Five Start -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-start justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Active Users
                        </h3>

                        <div x-data="{ openDropDown: false }" class="relative">
                            <button @click="openDropDown = !openDropDown"
                                :class="openDropDown ? 'text-gray-700 dark:text-white' :
                                    'text-gray-400 hover:text-gray-700 dark:hover:text-white'">
                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                        fill="" />
                                </svg>
                            </button>
                            <div x-show="openDropDown" @click.outside="openDropDown = false"
                                class="absolute right-0 z-40 w-40 p-2 space-y-1 bg-white border border-gray-200 shadow-theme-lg dark:bg-gray-dark top-full rounded-2xl dark:border-gray-800">
                                <button
                                    class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    View More
                                </button>
                                <button
                                    class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-end gap-1.5">
                        <div class="flex items-center gap-2.5">
                            <span class="relative inline-block w-5 h-5">
                                <span
                                    class="absolute w-2 h-2 transform -translate-x-1/2 -translate-y-1/2 rounded-full bg-error-500 top-1/2 left-1/2">
                                    <span
                                        class="absolute inline-flex w-4 h-4 rounded-full opacity-75 bg-error-400 -top-1 -left-1 animate-ping">
                                    </span> </span></span>
                            <span class="font-semibold text-gray-800 activeUsers text-title-sm dark:text-white/90">
                                364
                            </span>
                        </div>
                        <span class="block mb-1 text-gray-500 text-theme-sm dark:text-gray-400">
                            Live visitors
                        </span>
                    </div>

                    <div class="my-5 min-h-[155px] rounded-xl bg-gray-50 dark:bg-gray-900">
                        <div id="chartFive" class="-mr-2.5 -ml-[22px] h-full"></div>
                    </div>

                    <div class="flex items-center justify-center gap-6">
                        <div>
                            <p class="text-lg font-semibold text-center text-gray-800 dark:text-white/90">
                                224
                            </p>
                            <p class="text-theme-xs mt-0.5 text-center text-gray-500 dark:text-gray-400">
                                Avg, Daily
                            </p>
                        </div>

                        <div class="w-px bg-gray-200 h-11 dark:bg-gray-800"></div>

                        <div>
                            <p class="text-lg font-semibold text-center text-gray-800 dark:text-white/90">
                                1.4K
                            </p>
                            <p class="text-theme-xs mt-0.5 text-center text-gray-500 dark:text-gray-400">
                                Avg, Weekly
                            </p>
                        </div>

                        <div class="w-px bg-gray-200 h-11 dark:bg-gray-800"></div>

                        <div>
                            <p class="text-lg font-semibold text-center text-gray-800 dark:text-white/90">
                                22.1K
                            </p>
                            <p class="text-theme-xs mt-0.5 text-center text-gray-500 dark:text-gray-400">
                                Avg, Monthly
                            </p>
                        </div>
                    </div>
                </div>
                <!-- ====== Chart Five End -->
            </div>

            <div class="col-span-12 xl:col-span-7">
                <!-- ====== Chart Six Start -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Acquisition Channels
                        </h3>

                        <div x-data="{ openDropDown: false }" class="relative">
                            <button @click="openDropDown = !openDropDown"
                                :class="openDropDown ? 'text-gray-700 dark:text-white' :
                                    'text-gray-400 hover:text-gray-700 dark:hover:text-white'">
                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                        fill="" />
                                </svg>
                            </button>
                            <div x-show="openDropDown" @click.outside="openDropDown = false"
                                class="absolute right-0 top-full z-40 w-40 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark">
                                <button
                                    class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    View More
                                </button>
                                <button
                                    class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="custom-scrollbar max-w-full overflow-x-auto">
                        <div id="chartSix" class="-ml-5 min-w-[700px] pl-2"></div>
                    </div>
                </div>
                <!-- ====== Chart Six End -->
            </div>

            <div class="col-span-12 xl:col-span-5">
                <!-- ====== Chart Seven Start -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between mb-9">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Sessions By Device
                        </h3>
                        <div x-data="{ openDropDown: false }" class="relative">
                            <button @click="openDropDown = !openDropDown"
                                :class="openDropDown ? 'text-gray-700 dark:text-white' :
                                    'text-gray-400 hover:text-gray-700 dark:hover:text-white'">
                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                        fill="" />
                                </svg>
                            </button>
                            <div x-show="openDropDown" @click.outside="openDropDown = false"
                                class="absolute right-0 z-40 w-40 p-2 space-y-1 bg-white border border-gray-200 shadow-theme-lg dark:bg-gray-dark top-full rounded-2xl dark:border-gray-800">
                                <button
                                    class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    View More
                                </button>
                                <button
                                    class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div id="chartSeven" class="flex justify-center mx-auto chartDarkStyle"></div>
                    </div>
                </div>
                <!-- ====== Chart Seven End -->
            </div>

            <div class="col-span-12 xl:col-span-5">
                <!-- ====== Map One Start -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Customers Demographic
                            </h3>
                            <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                                Number of customer based on country
                            </p>
                        </div>

                        <div x-data="{ openDropDown: false }" class="relative h-fit">
                            <button @click="openDropDown = !openDropDown"
                                :class="openDropDown ? 'text-gray-700 dark:text-white' :
                                    'text-gray-400 hover:text-gray-700 dark:hover:text-white'">
                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                        fill="" />
                                </svg>
                            </button>
                            <div x-show="openDropDown" @click.outside="openDropDown = false"
                                class="absolute right-0 top-full z-40 w-40 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark">
                                <button
                                    class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    View More
                                </button>
                                <button
                                    class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <div
                        class="border-gary-200 my-6 overflow-hidden rounded-2xl border bg-gray-50 px-4 py-6 dark:border-gray-800 dark:bg-gray-900 sm:px-6">
                        <div id="mapOne"
                            class="mapOne map-btn -mx-4 -my-6 h-[212px] w-[252px] 2xsm:w-[307px] xsm:w-[358px] sm:-mx-6 md:w-[668px] lg:w-[634px] xl:w-[393px] 2xl:w-[554px]">
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-full max-w-8 items-center rounded-full">
                                    <img src="src/images/country/country-01.svg" alt="usa" />
                                </div>
                                <div>
                                    <p class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">
                                        USA
                                    </p>
                                    <span class="block text-theme-xs text-gray-500 dark:text-gray-400">
                                        2,379 Customers
                                    </span>
                                </div>
                            </div>

                            <div class="flex w-full max-w-[140px] items-center gap-3">
                                <div
                                    class="relative block h-2 w-full max-w-[100px] rounded-sm bg-gray-200 dark:bg-gray-800">
                                    <div
                                        class="absolute left-0 top-0 flex h-full w-[79%] items-center justify-center rounded-sm bg-brand-500 text-xs font-medium text-white">
                                    </div>
                                </div>
                                <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">
                                    79%
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-full max-w-8 items-center rounded-full">
                                    <img src="src/images/country/country-02.svg" alt="france" />
                                </div>
                                <div>
                                    <p class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">
                                        France
                                    </p>
                                    <span class="block text-theme-xs text-gray-500 dark:text-gray-400">
                                        589 Customers
                                    </span>
                                </div>
                            </div>

                            <div class="flex w-full max-w-[140px] items-center gap-3">
                                <div
                                    class="relative block h-2 w-full max-w-[100px] rounded-sm bg-gray-200 dark:bg-gray-800">
                                    <div
                                        class="absolute left-0 top-0 flex h-full w-[23%] items-center justify-center rounded-sm bg-brand-500 text-xs font-medium text-white">
                                    </div>
                                </div>
                                <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">
                                    23%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ====== Map One End -->
            </div>

            <div class="col-span-12 xl:col-span-7">
                <!-- ====== Table Two Start -->
                <div
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex flex-col gap-4 px-6 mb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Recent Orders
                            </h3>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                class="text-theme-sm shadow-theme-xs inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                <svg class="stroke-current fill-white dark:fill-gray-800" width="20" height="20"
                                    viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.29004 5.90393H17.7067" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M17.7075 14.0961H2.29085" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M12.0826 3.33331C13.5024 3.33331 14.6534 4.48431 14.6534 5.90414C14.6534 7.32398 13.5024 8.47498 12.0826 8.47498C10.6627 8.47498 9.51172 7.32398 9.51172 5.90415C9.51172 4.48432 10.6627 3.33331 12.0826 3.33331Z"
                                        fill="" stroke="" stroke-width="1.5" />
                                    <path
                                        d="M7.91745 11.525C6.49762 11.525 5.34662 12.676 5.34662 14.0959C5.34661 15.5157 6.49762 16.6667 7.91745 16.6667C9.33728 16.6667 10.4883 15.5157 10.4883 14.0959C10.4883 12.676 9.33728 11.525 7.91745 11.525Z"
                                        fill="" stroke="" stroke-width="1.5" />
                                </svg>

                                Filter
                            </button>

                            <button
                                class="text-theme-sm shadow-theme-xs inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                See all
                            </button>
                        </div>
                    </div>

                    <div class="max-w-full overflow-x-auto custom-scrollbar">
                        <table class="w-full">
                            <thead>
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <th class="px-6 py-3 text-left">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Products
                                        </p>
                                    </th>
                                    <th class="px-6 py-3 text-left">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Category
                                        </p>
                                    </th>
                                    <th class="px-6 py-3 text-left">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Country
                                        </p>
                                    </th>
                                    <th class="px-6 py-3 text-left">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            CR
                                        </p>
                                    </th>
                                    <th class="px-6 py-3 text-left">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Value
                                        </p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <td class="px-6 py-3.5">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            TailGrids
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">UI Kit</p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <div class="w-5 h-5 overflow-hidden rounded-full">
                                            <img src="src/images/country/country-01.svg" alt="country" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Dashboard
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-theme-sm text-success-600">$12,499</p>
                                    </td>
                                </tr>
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <td class="px-6 py-3.5">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            GrayGrids
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Templates
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <div class="w-5 h-5 overflow-hidden rounded-full">
                                            <img src="src/images/country/country-03.svg" alt="country" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Dashboard
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-theme-sm text-success-600">$5,498</p>
                                    </td>
                                </tr>
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <td class="px-6 py-3.5">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            Uideck
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Templates
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <div class="w-5 h-5 overflow-hidden rounded-full">
                                            <img src="src/images/country/country-04.svg" alt="country" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Dashboard
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-theme-sm text-success-600">$4,521</p>
                                    </td>
                                </tr>
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <td class="px-6 py-3.5">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            FormBold
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">SaaS</p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <div class="w-5 h-5 overflow-hidden rounded-full">
                                            <img src="src/images/country/country-05.svg" alt="country" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Dashboard
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-theme-sm text-success-600">$13,843</p>
                                    </td>
                                </tr>
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <td class="px-6 py-3.5">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            NextAdmin
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Dashboard
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <div class="w-5 h-5 overflow-hidden rounded-full">
                                            <img src="src/images/country/country-06.svg" alt="country" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Dashboard
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-theme-sm text-success-600">$7,523</p>
                                    </td>
                                </tr>
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <td class="px-6 py-3.5">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            Form Builder
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">SaaS</p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <div class="w-5 h-5 overflow-hidden rounded-full">
                                            <img src="src/images/country/country-07.svg" alt="country" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Dashboard
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-theme-sm text-success-600">$1,377</p>
                                    </td>
                                </tr>
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <td class="px-6 py-3.5">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            AyroUI
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">UI Kit</p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <div class="w-5 h-5 overflow-hidden rounded-full">
                                            <img src="src/images/country/country-08.svg" alt="country" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Dashboard
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-theme-sm text-success-600">$599,00</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- ====== Table Two End -->
            </div>
        </div>
    </div>
@endsection
