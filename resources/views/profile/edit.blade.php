@extends('layouts.app')
@section('title', 'Profile')
@section('subtitle', 'Manage your account settings')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6 animate-fade-in">

        {{-- Profile Information --}}
        <div class="glass rounded-2xl p-6">
            <h3 class="text-lg font-semibold text-white mb-1">Profile Information</h3>
            <p class="text-sm text-surface-400 mb-5">Update your account's name and email address.</p>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('patch')

                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-surface-300 mb-2">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                            autofocus
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                        @error('name')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-surface-300 mb-2">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                            required
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                        @error('email')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20">
                        Save Changes
                    </button>
                    @if (session('status') === 'profile-updated')
                        <p class="text-sm text-success-400 animate-fade-in">Saved.</p>
                    @endif
                </div>
            </form>
        </div>

        {{-- Update Password --}}
        <div class="glass rounded-2xl p-6">
            <h3 class="text-lg font-semibold text-white mb-1">Update Password</h3>
            <p class="text-sm text-surface-400 mb-5">Ensure your account is using a long, random password to stay secure.
            </p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf @method('put')

                <div class="space-y-5">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-surface-300 mb-2">Current
                            Password</label>
                        <div class="relative">
                            <input id="current_password" type="password" name="current_password"
                                autocomplete="current-password"
                                class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 pr-12 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                            <button type="button" onclick="togglePasswordVisibility('current_password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-surface-400 hover:text-surface-200 transition-colors"
                                tabindex="-1">
                                <svg id="current_password-eye-open" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="current_password-eye-closed" class="w-4 h-4 hidden" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('current_password', 'updatePassword')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-surface-300 mb-2">New Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" autocomplete="new-password"
                                class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 pr-12 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                            <button type="button" onclick="togglePasswordVisibility('password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-surface-400 hover:text-surface-200 transition-colors"
                                tabindex="-1">
                                <svg id="password-eye-open" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="password-eye-closed" class="w-4 h-4 hidden" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password', 'updatePassword')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-surface-300 mb-2">Confirm
                            New Password</label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                autocomplete="new-password"
                                class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 pr-12 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-surface-400 hover:text-surface-200 transition-colors"
                                tabindex="-1">
                                <svg id="password_confirmation-eye-open" class="w-4 h-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="password_confirmation-eye-closed" class="w-4 h-4 hidden" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation', 'updatePassword')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20">
                        Update Password
                    </button>
                    @if (session('status') === 'password-updated')
                        <p class="text-sm text-success-400 animate-fade-in">Password updated.</p>
                    @endif
                </div>
            </form>
        </div>

        {{-- Delete Account --}}
        {{-- <div class="glass rounded-2xl p-6 border border-danger-500/20">
        <h3 class="text-lg font-semibold text-danger-400 mb-1">Delete Account</h3>
        <p class="text-sm text-surface-400 mb-5">Once your account is deleted, all of its resources and data will be permanently deleted.</p>

        <form id="delete-account-form" method="POST" action="{{ route('profile.destroy') }}">
            @csrf @method('delete')

            <div class="mb-5">
                <label for="delete_password" class="block text-sm font-medium text-surface-300 mb-2">Confirm Password</label>
                <div class="relative">
                    <input id="delete_password" type="password" name="password" placeholder="Enter your password to confirm"
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 pr-12 text-sm text-surface-200 placeholder-surface-500 focus:border-danger-500 focus:ring-1 focus:ring-danger-500 focus:outline-none transition-all" />
                    <button type="button" onclick="togglePasswordVisibility('delete_password')" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-surface-400 hover:text-surface-200 transition-colors" tabindex="-1">
                        <svg id="delete_password-eye-open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg id="delete_password-eye-closed" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                @error('password', 'userDeletion') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
            </div>

            <button type="button" onclick="confirmDelete('delete-account-form', 'account')" class="px-6 py-2.5 rounded-xl bg-danger-500/15 border border-danger-500/30 text-danger-400 text-sm font-semibold hover:bg-danger-500/25 transition-all">
                Delete Account
            </button>
        </form>
    </div> --}}
    </div>
@endsection
