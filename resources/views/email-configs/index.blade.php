@extends('layouts.app')
@section('title', 'Email Configurations')
@section('subtitle', 'Manage SMTP settings')

@section('header-actions')
<a href="{{ route('email-configs.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Add Config
</a>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">
    @if($configurations->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($configurations as $config)
                <div class="glass rounded-2xl p-6 hover:border-primary-500/30 transition-all duration-200">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-base font-semibold text-white">{{ $config->name }}</h3>
                            <p class="text-sm text-surface-400 mt-1">{{ $config->from_name }} &lt;{{ $config->from_email }}&gt;</p>
                        </div>
                        <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-medium {{ $config->status === 'active' ? 'bg-success-500/15 text-success-400' : 'bg-surface-800 text-surface-500' }}">
                            {{ ucfirst($config->status) }}
                        </span>
                    </div>
                    <div class="space-y-2 mb-5">
                        <div class="flex items-center gap-2 text-xs text-surface-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                            {{ $config->host }}:{{ $config->port }} ({{ strtoupper($config->encryption) }})
                        </div>
                    </div>
                    <div class="flex items-center gap-2 pt-4 border-t border-surface-700/50">
                        <a href="{{ route('email-configs.edit', $config) }}" class="flex-1 text-center px-3 py-2 rounded-xl text-xs font-medium text-surface-300 hover:bg-surface-800 transition-all">Edit</a>
                        <form method="POST" action="{{ route('email-configs.test', $config) }}" class="flex-1" onsubmit="var email = prompt('Enter test email address:'); if(!email) return false; this.querySelector('[name=test_email]').value = email;">
                            @csrf
                            <input type="hidden" name="test_email" value="">
                            <button type="submit" class="w-full px-3 py-2 rounded-xl text-xs font-medium text-primary-400 hover:bg-primary-500/10 transition-all">Test</button>
                        </form>
                        <form id="delete-config-{{ $config->id }}" method="POST" action="{{ route('email-configs.destroy', $config) }}">@csrf @method('DELETE')</form>
                        <button onclick="confirmDelete('delete-config-{{ $config->id }}', 'configuration')" class="px-3 py-2 rounded-xl text-xs font-medium text-danger-400 hover:bg-danger-500/10 transition-all">Delete</button>
                    </div>
                </div>
            @endforeach
        </div>
        <div>{{ $configurations->links() }}</div>
    @else
        <div class="glass rounded-2xl text-center py-16">
            <svg class="w-16 h-16 mx-auto text-surface-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <h3 class="text-lg font-semibold text-surface-300 mb-2">No email configurations</h3>
            <p class="text-sm text-surface-500 mb-6">Add your first SMTP configuration to start sending emails.</p>
            <a href="{{ route('email-configs.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-400 transition-all">Add Configuration</a>
        </div>
    @endif
</div>
@endsection
