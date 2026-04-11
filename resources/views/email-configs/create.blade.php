@extends('layouts.app')
@section('title', 'Add Email Configuration')
@section('subtitle', 'Configure a new SMTP sender')

@section('content')
<div class="max-w-2xl mx-auto animate-fade-in">
    <form method="POST" action="{{ route('email-configs.store') }}" autocomplete="off">
        @csrf
        <div class="glass rounded-2xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-white mb-5">SMTP Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-surface-300 mb-2">Configuration Name <span class="text-danger-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Company Gmail"
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                    @error('name') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">From Name <span class="text-danger-400">*</span></label>
                    <input type="text" name="from_name" value="{{ old('from_name') }}" required
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                    @error('from_name') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">From Email <span class="text-danger-400">*</span></label>
                    <input type="email" name="from_email" value="{{ old('from_email') }}" required
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                    @error('from_email') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">SMTP Host <span class="text-danger-400">*</span></label>
                    <input type="text" name="host" value="{{ old('host') }}" required placeholder="smtp.gmail.com"
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                    @error('host') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">SMTP Port <span class="text-danger-400">*</span></label>
                    <input type="number" name="port" value="{{ old('port', 587) }}" required
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                    @error('port') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Encryption <span class="text-danger-400">*</span></label>
                    <select name="encryption" class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                        <option value="tls" {{ old('encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ old('encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="none" {{ old('encryption') == 'none' ? 'selected' : '' }}>None</option>
                    </select>
                    @error('encryption') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Username <span class="text-danger-400">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                    @error('username') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Password <span class="text-danger-400">*</span></label>
                    <div class="relative">
                        <input id="smtp-password" type="password" name="password" required
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 pr-12 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                        <button type="button" onclick="togglePasswordVisibility('smtp-password')" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-surface-400 hover:text-surface-200 transition-colors" tabindex="-1">
                            <svg id="smtp-password-eye-open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg id="smtp-password-eye-closed" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('password') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Status</label>
                    <select name="status" class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('email-configs.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-surface-400 hover:text-surface-200 transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20">
                Create Configuration
            </button>
        </div>
    </form>
</div>
@endsection
