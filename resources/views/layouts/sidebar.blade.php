<aside :class="sidebarToggle ? 'translate-x-0 xl:w-[90px]' : '-translate-x-full'"
    class="sidebar fixed top-0 left-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-auto border-r border-gray-200 bg-white px-5 transition-all duration-300 xl:static xl:translate-x-0 dark:border-gray-800 dark:bg-black"
    @click.outside="sidebarToggle = false">
    <!-- SIDEBAR HEADER -->
    <div :class="sidebarToggle ? 'justify-center' : 'justify-between'"
        class="sidebar-header flex items-center gap-2 pt-4 pb-2">
        <a href="{{ route('dashboard') }}">
            <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
                <img class="w-24 dark:hidden" src="{{ asset('assets/images/logo-w.png') }}" alt="Logo" />
                <img class="w-24 hidden dark:block" src="{{ asset('assets/images/logo-w.png') }}" alt="Logo" />
            </span>

            <img class="logo-icon" :class="sidebarToggle ? 'xl:block' : 'hidden'"
                src="{{ asset('assets/images/logo.png') }}" alt="Logo" />
        </a>
    </div>
    <!-- SIDEBAR HEADER -->

    <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
        <!-- Sidebar Menu -->
        <nav x-data="{ selected: $persist('Dashboard') }">
            <!-- Menu Group -->
            <div>
                <h3 class="mb-4 text-xs leading-[20px] text-gray-400 uppercase">
                    <span class="menu-group-title" :class="sidebarToggle ? 'xl:hidden' : ''">
                        {{ __('auth.menu') }}
                    </span>

                    <svg :class="sidebarToggle ? 'xl:block hidden' : 'hidden'"
                        class="menu-group-icon mx-auto fill-current" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
                            fill="currentColor" />
                    </svg>
                </h3>

                <ul class="mb-6 flex flex-col gap-1">
                    <!-- Menu Item Dashboard -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Dashboard' ? '':'Dashboard')"
                            class="menu-item group"
                            :class="(selected === 'Dashboard') || (page === 'ecommerce' || page === 'analytics' ||
                                page === 'marketing' || page === 'crm' || page === 'stocks' ||
                                page === 'saas' || page === 'logistics') ? 'menu-item-active' : 'menu-item-inactive'">
                            <svg :class="(selected === 'Dashboard') || (page === 'ecommerce' || page === 'analytics' ||
                                page === 'marketing' || page === 'crm' || page === 'stocks') ?
                            'menu-item-icon-active' : 'menu-item-icon-inactive'"
                                width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z"
                                    fill="currentColor" />
                            </svg>

                            <span class="menu-item-text" :class="sidebarToggle ? 'xl:hidden' : ''">
                                {{ __('auth.dashboard') }}
                            </span>

                            <svg class="menu-item-arrow"
                                :class="[(selected === 'Dashboard') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive',
                                    sidebarToggle ? 'xl:hidden' : ''
                                ]"
                                width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>

                        <!-- Dropdown Menu Start -->
                        <div class="translate transform overflow-hidden"
                            :class="(selected === 'Dashboard') ? 'block' : 'hidden'">
                            <ul :class="sidebarToggle ? 'xl:hidden' : 'flex'"
                                class="menu-dropdown mt-2 flex flex-col gap-1 pl-9">
                                <li>
                                    <a href="index.html" class="menu-dropdown-item group"
                                        :class="page === 'ecommerce' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        eCommerce
                                    </a>
                                </li>
                                <li>
                                    <a href="analytics.html" class="menu-dropdown-item group"
                                        :class="page === 'analytics' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        Analytics
                                    </a>
                                </li>
                                <li>
                                    <a class="menu-dropdown-item group" href="marketing.html"
                                        :class="page === 'marketing' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        Marketing
                                    </a>
                                </li>
                                <li>
                                    <a href="crm.html" class="menu-dropdown-item group"
                                        :class="page === 'crm' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        CRM
                                    </a>
                                </li>
                                <li>
                                    <a href="stocks.html" class="menu-dropdown-item group"
                                        :class="page === 'stocks' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        Stocks
                                    </a>
                                </li>
                                <li>
                                    <a href="saas.html" class="menu-dropdown-item group"
                                        :class="page === 'saas' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        SaaS
                                        <span class="absolute right-3 flex items-center gap-1">
                                            <span class="menu-dropdown-badge"
                                                :class="page === 'saas' ? 'menu-dropdown-badge-active' :
                                                    'menu-dropdown-badge-inactive'">
                                                New
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="logistics.html" class="menu-dropdown-item group"
                                        :class="page === 'logistics' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        Logistics
                                        <span class="absolute right-3 flex items-center gap-1">
                                            <span class="menu-dropdown-badge"
                                                :class="page === 'logistics' ? 'menu-dropdown-badge-active' :
                                                    'menu-dropdown-badge-inactive'">
                                                New
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- Dropdown Menu End -->
                    </li>
                    <!-- Menu Item Dashboard -->
                </ul>
            </div>
        </nav>
        <!-- Sidebar Menu -->
    </div>

    <!-- Language Switcher -->
    {{-- <div class="mt-auto pb-4" :class="sidebarToggle ? 'xl:hidden' : ''">
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="menu-item group w-full flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5">
                <svg class="fill-gray-500 group-hover:fill-gray-700 dark:fill-gray-400 dark:group-hover:fill-gray-300"
                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M9.5 2C8.11929 2 7 3.11929 7 4.5V19.5C7 20.8807 8.11929 22 9.5 22H14.5C15.8807 22 17 20.8807 17 19.5V4.5C17 3.11929 15.8807 2 14.5 2H9.5ZM8.5 4.5C8.5 3.94772 8.94772 3.5 9.5 3.5H14.5C15.0523 3.5 15.5 3.94772 15.5 4.5V19.5C15.5 20.0523 15.0523 20.5 14.5 20.5H9.5C8.94772 20.5 8.5 20.0523 8.5 19.5V4.5ZM10 6C10 5.44772 10.4477 5 11 5H13C13.5523 5 14 5.44772 14 6C14 6.55228 13.5523 7 13 7H11C10.4477 7 10 6.55228 10 6ZM10 10C10 9.44772 10.4477 9 11 9H13C13.5523 9 14 9.44772 14 10C14 10.5523 13.5523 11 13 11H11C10.4477 11 10 10.5523 10 10ZM10 14C10 13.4477 10.4477 13 11 13H13C13.5523 13 14 13.4477 14 14C14 14.5523 13.5523 15 13 15H11C10.4477 15 10 14.5523 10 14ZM10 18C10 17.4477 10.4477 17 11 17H12C12.5523 17 13 17.4477 13 18C13 18.5523 12.5523 19 12 19H11C10.4477 19 10 18.5523 10 18Z"
                        fill="currentColor" />
                </svg>
                <span class="menu-item-text flex-1 text-left">{{ strtoupper(app()->getLocale()) }}</span>
                <svg class="menu-item-arrow" :class="open ? 'rotate-180' : ''" width="20" height="20"
                    viewBox="0 0 20 20" fill="none">
                    <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="currentColor"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <div x-show="open" @click.away="open = false" x-transition
                class="absolute bottom-full left-0 mb-2 w-full rounded-lg bg-white shadow-lg dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
                <a href="{{ route('lang.switch', 'en') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ app()->getLocale() === 'en' ? 'bg-brand-50 text-brand-600 dark:bg-brand-900' : '' }}">
                    English
                </a>
                <a href="{{ route('lang.switch', 'sw') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 {{ app()->getLocale() === 'sw' ? 'bg-brand-50 text-brand-600 dark:bg-brand-900' : '' }}">
                    Kiswahili
                </a>
            </div>
        </div>
    </div> --}}
    <!-- Language Switcher End -->
</aside>
