@extends('layouts.app')
@section('title', 'Email Logs')
@section('subtitle', auth()->user()->isAdmin() ? 'All email activity' : 'Your email activity')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="glass rounded-2xl overflow-hidden">
        @if($logs->count() > 0)
            <table class="w-full">
                <thead>
                    <tr class="border-b border-surface-700/50">
                        <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Subject</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden md:table-cell">To</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden lg:table-cell">Contact</th>
                        @if(auth()->user()->isAdmin())
                            <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden xl:table-cell">Sent By</th>
                        @endif
                        <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden sm:table-cell">Template</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-800/50">
                    @foreach($logs as $log)
                        <tr class="hover:bg-surface-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-medium {{ $log->status === 'sent' ? 'bg-success-500/15 text-success-400' : ($log->status === 'failed' ? 'bg-danger-500/15 text-danger-400' : 'bg-warning-500/15 text-warning-400') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-surface-200 max-w-xs truncate">{{ $log->subject }}</td>
                            <td class="px-6 py-4 text-sm text-surface-400 hidden md:table-cell max-w-xs truncate">{{ is_array($log->recipients) ? implode(', ', $log->recipients) : $log->recipients }}</td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                @if($log->contact)
                                    <a href="{{ route('contacts.show', $log->contact) }}" class="text-sm text-primary-400 hover:text-primary-300">{{ $log->contact->name }}</a>
                                @else
                                    <span class="text-sm text-surface-500">Deleted</span>
                                @endif
                            </td>
                            @if(auth()->user()->isAdmin())
                                <td class="px-6 py-4 text-sm text-surface-400 hidden xl:table-cell">{{ $log->user->name ?? 'N/A' }}</td>
                            @endif
                            <td class="px-6 py-4 text-sm text-surface-400 hidden sm:table-cell">{{ $log->template->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-surface-500">{{ ($log->sent_at ?? $log->created_at)->format('M d, H:i') }}</td>
                        </tr>
                        @if($log->status === 'failed' && $log->error)
                            <tr class="bg-danger-500/5">
                                <td colspan="7" class="px-6 py-2 text-xs text-danger-400">Error: {{ $log->error }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-surface-800/50">{{ $logs->links() }}</div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-surface-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <h3 class="text-lg font-semibold text-surface-300 mb-2">No email logs</h3>
                <p class="text-sm text-surface-500">Email activity will appear here once you send your first email.</p>
            </div>
        @endif
    </div>
</div>
@endsection
