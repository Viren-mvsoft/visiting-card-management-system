@extends('layouts.app')
@section('title', 'Events')
@section('subtitle', 'Manage your event list and track associated contacts')

@section('header-actions')
<a href="{{ route('events.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20 hover:shadow-xl">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Add Event
</a>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Search -->
    <div class="glass rounded-2xl p-5">
        <form method="GET" action="{{ route('events.index') }}">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events by name, location, or description..."
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-surface-800 text-surface-300 text-sm font-medium hover:bg-surface-700 border border-surface-700 transition-all">
                    Search
                </button>
                @if(request()->has('search'))
                    <a href="{{ route('events.index') }}" class="px-5 py-2.5 rounded-xl text-surface-400 text-sm font-medium hover:text-surface-200 transition-all">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Events Table -->
    <div class="glass rounded-2xl overflow-hidden">
        @if($events->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-surface-700/50">
                            <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Event Name</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Date</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden md:table-cell">Location</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Contacts</th>
                            <th class="text-right px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-800/50">
                        @foreach($events as $event)
                            <tr class="hover:bg-surface-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-primary-500/20 to-accent-500/20 flex items-center justify-center text-primary-400 font-semibold text-xs border border-primary-500/20">
                                            {{ strtoupper(substr($event->name, 0, 1)) }}
                                        </div>
                                        <a href="{{ route('events.show', $event) }}" class="text-sm font-medium text-surface-200 hover:text-white transition-colors">{{ $event->name }}</a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-surface-400">
                                    {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('M d, Y') : '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-surface-400 hidden md:table-cell">
                                    {{ $event->location ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-surface-400">
                                    <span class="inline-flex px-2 py-1 rounded-lg bg-surface-800 border border-surface-700 text-xs font-medium text-surface-300">
                                        {{ $event->contacts_count }} contacts
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('events.show', $event) }}" class="p-2 rounded-lg text-surface-400 hover:text-primary-400 hover:bg-primary-500/10 transition-all" title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('events.edit', $event) }}" class="p-2 rounded-lg text-surface-400 hover:text-warning-400 hover:bg-warning-500/10 transition-all" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form id="delete-event-{{ $event->id }}" method="POST" action="{{ route('events.destroy', $event) }}" class="inline">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button onclick="confirmDelete('delete-event-{{ $event->id }}', 'event')" 
                                            class="p-2 rounded-lg text-surface-400 hover:text-danger-400 hover:bg-danger-500/10 transition-all {{ $event->contacts_count > 0 ? 'opacity-30 cursor-not-allowed' : '' }}" 
                                            {{ $event->contacts_count > 0 ? 'disabled' : '' }}
                                            title="{{ $event->contacts_count > 0 ? 'Cannot delete event with contacts' : 'Delete' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-surface-800/50">
                {{ $events->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-surface-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <h3 class="text-lg font-semibold text-surface-300 mb-2">No events found</h3>
                <p class="text-sm text-surface-500 mb-6">Create your first event to start organizing your contacts.</p>
                <a href="{{ route('events.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-400 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Event
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
