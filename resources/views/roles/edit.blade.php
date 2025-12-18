@extends('layouts.app')

@section('title', config('app.name') . ' - Edit Role')

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
                                <svg class="fill-brand-500" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10 2C13.1046 2 15.6667 4.56211 15.6667 7.66667C15.6667 10.7712 13.1046 13.3333 10 13.3333C6.89543 13.3333 4.33333 10.7712 4.33333 7.66667C4.33333 4.56211 6.89543 2 10 2ZM10 3.33333C7.60761 3.33333 5.66667 5.27428 5.66667 7.66667C5.66667 10.0591 7.60761 12 10 12C12.3924 12 14.3333 10.0591 14.3333 7.66667C14.3333 5.27428 12.3924 3.33333 10 3.33333ZM10 14.3333C6.93172 14.3333 4.20556 15.6944 2.50033 17.6667C3.26392 18.3918 4.12452 19.0065 5.05585 19.4893C6.62143 20.2758 8.27936 20.6667 10 20.6667C11.7206 20.6667 13.3786 20.2758 14.9441 19.4893C15.8755 19.0065 16.7361 18.3918 17.4997 17.6667C15.7944 15.6944 13.0683 14.3333 10 14.3333Z"
                                        fill="currentColor" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90 sm:text-2xl">Edit Role
                                </h2>
                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Update role information and
                                    permissions</p>
                            </div>
                        </div>
                        <a href="{{ route('roles.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.05]">
                            <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.0002 12.6666L5.3335 7.99998L10.0002 3.33331" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Back
                        </a>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="mt-4 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <!-- Card Header -->
                    <div
                        class="border-b border-gray-100 bg-gray-50/50 px-5 py-4 dark:border-gray-800 dark:bg-gray-900/50 sm:px-6">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Role Details</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Basic information about the role</p>
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

                        <form method="POST" action="{{ route('roles.update', $role) }}" class="space-y-6">
                            @csrf
                            @method('PATCH')

                            <!-- Role Name and Guard Name in Same Row -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- Role Name Field -->
                                <div class="space-y-2">
                                    <label for="name"
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        Role Name
                                        <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="name" name="name"
                                            value="{{ old('name', $role->name) }}" required
                                            class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-800 shadow-sm transition-colors placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:border-brand-500 @error('name') border-error-500 focus:border-error-500 focus:ring-error-500/20 @enderror"
                                            placeholder="Enter role name">
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

                                <!-- Guard Name (Read-only) -->
                                <div class="space-y-2">
                                    <label for="guard_name"
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Guard Name
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="guard_name" value="{{ $role->guard_name }}" disabled
                                            class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-medium text-gray-500 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-400">
                                    </div>
                                    <p class="mt-1 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Guard name cannot be changed
                                    </p>
                                </div>
                            </div>

                            <!-- Permissions Section -->
                            <div class="mt-6 space-y-2">
                                <div class="flex items-center justify-between">
                                    <label
                                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                        </svg>
                                        Permissions
                                    </label>
                                    @if (!empty($permissionsByCategory))
                                        <div
                                            class="hidden items-center gap-2 rounded-lg bg-brand-500/10 px-3 py-1.5 text-sm font-medium text-brand-700 dark:bg-brand-500/20 dark:text-brand-400 sm:flex">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span id="selectedCount">{{ count($rolePermissions) }}</span>
                                            selected
                                        </div>
                                    @endif
                                </div>

                                @if (empty($permissionsByCategory))
                                    <div
                                        class="rounded-lg border border-gray-200 bg-gray-50 p-8 text-center dark:border-gray-800 dark:bg-gray-800/50">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                        </svg>
                                        <p class="mt-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            No permissions
                                            available</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create
                                            permissions first
                                            to assign them to roles</p>
                                    </div>
                                @else
                                    <div class="mb-3 flex items-center gap-2">
                                        <button type="button" onclick="selectAll()"
                                            class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                                            Select All
                                        </button>
                                        <span class="text-gray-300 dark:text-gray-700">|</span>
                                        <button type="button" onclick="deselectAll()"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                            Deselect All
                                        </button>
                                    </div>

                                    <div
                                        class="max-h-[500px] space-y-6 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-900/50">
                                        @foreach ($permissionsByCategory as $category => $permissions)
                                            <div class="space-y-3">
                                                <!-- Category Header -->
                                                <div
                                                    class="flex items-center gap-2 border-b border-gray-200 pb-2 dark:border-gray-700">
                                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                                        {{ $category }}
                                                    </h4>
                                                    <span
                                                        class="rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                        {{ count($permissions) }}
                                                    </span>
                                                </div>

                                                <!-- Permissions Grid for this Category -->
                                                <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 lg:grid-cols-4">
                                                    @foreach ($permissions as $permission)
                                                        <label
                                                            class="group relative flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white p-3.5 transition-all hover:border-brand-300 hover:bg-brand-50/50 hover:shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:hover:border-brand-700 dark:hover:bg-brand-500/5">
                                                            <div class="flex h-5 items-center">
                                                                <input type="checkbox" name="permissions[]"
                                                                    value="{{ $permission->id }}"
                                                                    {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                                    onchange="updateCount()"
                                                                    class="h-4 w-4 rounded border-gray-300 text-brand-500 transition-colors focus:ring-2 focus:ring-brand-500/20 focus:ring-offset-0 dark:border-gray-600 dark:bg-gray-800 dark:ring-offset-gray-900">
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <span
                                                                    class="block text-sm font-medium text-gray-700 group-hover:text-brand-700 dark:text-gray-300 dark:group-hover:text-brand-400">
                                                                    {{ $permission->name }}
                                                                </span>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <p class="mt-3 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Check the permissions you want to assign to this role
                                    </p>
                                    @error('permissions')
                                        <p class="mt-2 flex items-center gap-1.5 text-sm text-error-600 dark:text-error-400">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                @endif
                            </div>

                            <!-- Form Actions -->
                            <div
                                class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 dark:border-gray-800 sm:flex-row sm:justify-end">
                                <a href="{{ route('roles.index') }}"
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
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Update Role
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function updateCount() {
                const checkboxes = document.querySelectorAll('input[name="permissions[]"]:checked');
                const countElement = document.getElementById('selectedCount');
                if (countElement) {
                    countElement.textContent = checkboxes.length;
                }
            }

            function selectAll() {
                document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                    cb.checked = true;
                });
                updateCount();
            }

            function deselectAll() {
                document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                    cb.checked = false;
                });
                updateCount();
            }

            // Update count on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateCount();
            });
        </script>
    @endpush
@endsection
