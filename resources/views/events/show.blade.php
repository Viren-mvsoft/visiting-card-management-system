@extends('layouts.app')
@section('title', $event->name)
@section('subtitle', 'Event details and associated contacts')

@section('header-actions')
<div class="flex items-center gap-3">
    <a href="{{ route('events.edit', $event) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-800 text-surface-200 text-sm font-semibold hover:bg-surface-700 border border-surface-700 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit Event
    </a>
    <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-surface-400 text-sm font-medium hover:text-surface-200 transition-all">
        Back to Events
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Event Stats -->
        <div class="glass rounded-2xl p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-surface-500 mb-4">Event date</h3>
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-xl bg-primary-500/10 text-primary-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-xl font-bold text-white">{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('M d, Y') : 'Not Set' }}</span>
                </div>
            </div>
        </div>

        <div class="glass rounded-2xl p-6">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-surface-500 mb-4">Location</h3>
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-accent-500/10 text-accent-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-xl font-bold text-white">{{ $event->location ?? 'Not Specified' }}</span>
            </div>
        </div>

        <div class="glass rounded-2xl p-6">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-surface-500 mb-4">Contacts</h3>
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-success-500/10 text-success-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-3xl font-bold text-white">{{ $event->contacts_count }}</span>
            </div>
        </div>
    </div>

    @if($event->description)
        <div class="glass rounded-2xl p-6">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-surface-500 mb-3">Event Description</h3>
            <p class="text-surface-300 leading-relaxed whitespace-pre-line">{{ $event->description }}</p>
        </div>
    @endif

    <!-- Associated Contacts -->
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-white">Contacts from this event</h3>
        <div class="glass rounded-2xl overflow-hidden">
            @if($contacts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-surface-700/50">
                                <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Name</th>
                                <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Company</th>
                                <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden sm:table-cell">Contact Info</th>
                                <th class="text-right px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-800/50">
                            @foreach($contacts as $contact)
                                <tr class="hover:bg-surface-800/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-surface-800 flex items-center justify-center text-primary-400 font-semibold text-xs border border-surface-700">
                                                {{ strtoupper(substr($contact->name, 0, 1)) }}
                                            </div>
                                            <a href="{{ route('contacts.show', $contact) }}" class="text-sm font-medium text-surface-200 hover:text-white transition-colors">{{ $contact->name }}</a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-surface-400">{{ $contact->company_name ?? '—' }}</td>
                                    <td class="px-6 py-4 hidden sm:table-cell">
                                        <div class="space-y-1">
                                            @if($contact->phones->first())
                                                <div class="flex items-center gap-1.5 text-xs text-surface-400">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    {{ $contact->phones->first()->phone }}
                                                </div>
                                            @endif
                                            @if($contact->emails->first())
                                                <div class="flex items-center gap-1.5 text-xs text-surface-500">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                    {{ $contact->emails->first()->email }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <a href="{{ route('contacts.show', $contact) }}" class="text-primary-400 hover:text-primary-300 font-medium transition-colors">View Card</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-surface-800/50">
                    {{ $contacts->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-sm text-surface-500">No contacts linked to this event yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
