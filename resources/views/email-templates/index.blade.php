@extends('layouts.app')
@section('title', 'Email Templates')
@section('subtitle', 'Manage email templates')

@section('header-actions')
<a href="{{ route('email-templates.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    New Template
</a>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">
    @if($templates->count() > 0)
        <div class="glass rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-surface-700/50">
                        <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Name</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden sm:table-cell">Subject</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden md:table-cell">Created By</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-800/50">
                    @foreach($templates as $template)
                        <tr class="hover:bg-surface-800/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-surface-200">{{ $template->name }}</td>
                            <td class="px-6 py-4 text-sm text-surface-400 hidden sm:table-cell truncate max-w-xs">{{ $template->subject }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-medium {{ $template->status === 'active' ? 'bg-success-500/15 text-success-400' : 'bg-surface-800 text-surface-500' }}">
                                    {{ ucfirst($template->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-surface-400 hidden md:table-cell">{{ $template->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('email-templates.edit', $template) }}" class="p-2 rounded-lg text-surface-400 hover:text-warning-400 hover:bg-warning-500/10 transition-all" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('email-templates.duplicate', $template) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 rounded-lg text-surface-400 hover:text-primary-400 hover:bg-primary-500/10 transition-all" title="Duplicate">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </button>
                                    </form>
                                    <form id="delete-template-{{ $template->id }}" method="POST" action="{{ route('email-templates.destroy', $template) }}">@csrf @method('DELETE')</form>
                                    <button onclick="confirmDelete('delete-template-{{ $template->id }}', 'template')" class="p-2 rounded-lg text-surface-400 hover:text-danger-400 hover:bg-danger-500/10 transition-all" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>{{ $templates->links() }}</div>
    @else
        <div class="glass rounded-2xl text-center py-16">
            <svg class="w-16 h-16 mx-auto text-surface-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/></svg>
            <h3 class="text-lg font-semibold text-surface-300 mb-2">No templates yet</h3>
            <p class="text-sm text-surface-500 mb-6">Create your first email template to start sending personalized emails.</p>
            <a href="{{ route('email-templates.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-400 transition-all">New Template</a>
        </div>
    @endif

    <!-- Variable Reference -->
    <div class="glass rounded-2xl p-6">
        <h3 class="text-sm font-semibold uppercase tracking-wider text-surface-400 mb-4">Available Template Variables</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            @php
                $varMap = [
                    '@{{name}}' => 'Contact Name',
                    '@{{company}}' => 'Company',
                    '@{{event}}' => 'Event',
                    '@{{country}}' => 'Country',
                    '@{{sender_name}}' => 'Sender Name',
                ];
            @endphp
            @foreach($varMap as $var => $desc)
                <div class="px-3 py-2 rounded-lg bg-surface-800 border border-surface-700">
                    <code class="text-xs text-primary-400">{{ str_replace('@', '', $var) }}</code>
                    <p class="text-xs text-surface-500 mt-0.5">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
