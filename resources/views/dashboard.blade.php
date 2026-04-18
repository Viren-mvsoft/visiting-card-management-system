@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', auth()->user()->isAdmin() ? 'Overview of all activity' : 'Your activity overview')

@section('content')
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- Total Contacts -->
            <div class="glass rounded-2xl p-6 animate-fade-in stagger-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-surface-400">Total Contacts</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ number_format($totalContacts) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-primary-500/15 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Emails Sent (30d) -->
            <div class="glass rounded-2xl p-6 animate-fade-in stagger-2">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-surface-400">Emails Sent (30d)</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ number_format($emailsSent) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-accent-500/15 flex items-center justify-center">
                        <svg class="w-6 h-6 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Top Events -->
            <div class="glass rounded-2xl p-6 animate-fade-in stagger-3">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-medium text-surface-400">Top Events</p>
                    <div class="w-12 h-12 rounded-xl bg-warning-500/15 flex items-center justify-center">
                        <svg class="w-6 h-6 text-warning-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                @if ($topEvents->count() > 0)
                    <div class="space-y-2">
                        @foreach ($topEvents->take(3) as $event)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-surface-300 truncate">{{ $event->event?->name ?? 'N/A' }}</span>
                                <span
                                    class="text-xs font-medium px-2 py-0.5 rounded-full bg-surface-800 text-surface-400">{{ $event->count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-surface-500">No events yet</p>
                @endif
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Contacts -->
            <div class="glass rounded-2xl p-6 animate-fade-in stagger-4">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-white">Recent Contacts</h3>
                    <a href="{{ route('contacts.index') }}"
                        class="text-sm text-primary-400 hover:text-primary-300 transition-colors">View all →</a>
                </div>
                @if ($recentContacts->count() > 0)
                    <div class="space-y-3">
                        @foreach ($recentContacts as $contact)
                            <a href="{{ route('contacts.show', $contact) }}"
                                class="flex items-center gap-4 p-3 rounded-xl hover:bg-surface-800/50 transition-all duration-200 group">
                                <div
                                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500/20 to-accent-500/20 flex items-center justify-center text-primary-400 font-semibold text-sm border border-primary-500/20">
                                    {{ strtoupper(substr($contact->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-medium text-surface-200 group-hover:text-white transition-colors truncate">
                                        {{ $contact->name }}</p>
                                    <p class="text-xs text-surface-500 truncate">
                                        {{ $contact->company_name ?? 'No company' }} ·
                                        {{ $contact->country->name ?? 'N/A' }}</p>
                                </div>
                                <span class="text-xs text-surface-500">{{ $contact->created_at->diffForHumans() }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-surface-700 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-sm text-surface-500">No contacts yet</p>
                        <a href="{{ route('contacts.create') }}"
                            class="mt-3 inline-block text-sm text-primary-400 hover:text-primary-300">Add your first contact
                            →</a>
                    </div>
                @endif
            </div>

            <!-- Recent Emails -->
            <div class="glass rounded-2xl p-6 animate-fade-in stagger-5">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-white">Recent Emails</h3>
                    <a href="{{ route('email-logs.index') }}"
                        class="text-sm text-primary-400 hover:text-primary-300 transition-colors">View all →</a>
                </div>
                @if ($recentEmails->count() > 0)
                    <div class="space-y-3">
                        @foreach ($recentEmails as $log)
                            <div
                                class="flex items-center gap-4 p-3 rounded-xl hover:bg-surface-800/50 transition-all duration-200">
                                <div
                                    class="w-10 h-10 rounded-xl flex items-center justify-center {{ $log->status === 'sent' ? 'bg-success-500/15 text-success-400' : ($log->status === 'failed' ? 'bg-danger-500/15 text-danger-400' : 'bg-warning-500/15 text-warning-400') }}">
                                    @if ($log->status === 'sent')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @elseif($log->status === 'failed')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-surface-200 truncate">{{ $log->subject }}</p>
                                    <p class="text-xs text-surface-500 truncate">To:
                                        {{ is_array($log->recipients) ? implode(', ', $log->recipients) : $log->recipients }}
                                    </p>
                                </div>
                                <span class="text-xs text-surface-500">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-surface-700 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm text-surface-500">No emails sent yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
