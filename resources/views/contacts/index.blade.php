@extends('layouts.app')
@section('title', 'Contacts')
@section('subtitle', auth()->user()->isAdmin() ? 'All visiting cards' : 'Your visiting cards')

@section('header-actions')
<a href="{{ route('contacts.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20 hover:shadow-xl">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Add Contact
</a>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Search & Filters -->
    <div class="glass rounded-2xl p-5">
        <form method="GET" action="{{ route('contacts.index') }}" class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search contacts..."
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                </div>
                <select name="country" class="rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                    @endforeach
                </select>
                <select name="event" class="rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                    <option value="">All Events</option>
                    @foreach($events as $event)
                        <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>{{ $event }}</option>
                    @endforeach
                </select>
                <select name="per_page" class="rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all min-w-[100px]">
                    <option value="25" {{ request('per_page', '25') == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                    <option value="500" {{ request('per_page') == '500' ? 'selected' : '' }}>All</option>
                </select>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-surface-800 text-surface-300 text-sm font-medium hover:bg-surface-700 border border-surface-700 transition-all">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'country', 'event']))
                    <a href="{{ route('contacts.index') }}" class="px-5 py-2.5 rounded-xl text-surface-400 text-sm font-medium hover:text-surface-200 transition-all">Clear</a>
                @endif
            </div>
            @if($contacts->total() > 0)
                <div class="text-xs text-surface-500">
                    Showing {{ $contacts->firstItem() }}–{{ $contacts->lastItem() }} of {{ $contacts->total() }} contacts
                </div>
            @endif
        </form>
    </div>

    <!-- Contacts Table -->
    <div class="glass rounded-2xl overflow-hidden">
        @if($contacts->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-surface-700/50">
                            <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Name</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Company</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden md:table-cell">Country</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden lg:table-cell">Event</th>
                            @if(auth()->user()->isAdmin())
                                <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden xl:table-cell">Created By</th>
                            @endif
                            <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden sm:table-cell">Date</th>
                            <th class="text-right px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-800/50">
                        @foreach($contacts as $contact)
                            <tr class="hover:bg-surface-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-primary-500/20 to-accent-500/20 flex items-center justify-center text-primary-400 font-semibold text-xs border border-primary-500/20">
                                            {{ strtoupper(substr($contact->name, 0, 1)) }}
                                        </div>
                                        <a href="{{ route('contacts.show', $contact) }}" class="text-sm font-medium text-surface-200 hover:text-white transition-colors">{{ $contact->name }}</a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-surface-400">{{ $contact->company_name ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-surface-400 hidden md:table-cell">{{ $contact->country ?? '—' }}</td>
                                <td class="px-6 py-4 hidden lg:table-cell">
                                    @if($contact->event)
                                        <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-medium bg-primary-500/10 text-primary-400 border border-primary-500/20">{{ $contact->event }}</span>
                                    @else
                                        <span class="text-sm text-surface-500">—</span>
                                    @endif
                                </td>
                                @if(auth()->user()->isAdmin())
                                    <td class="px-6 py-4 text-sm text-surface-400 hidden xl:table-cell">{{ $contact->user->name ?? 'N/A' }}</td>
                                @endif
                                <td class="px-6 py-4 text-sm text-surface-500 hidden sm:table-cell">{{ $contact->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('contacts.show', $contact) }}" class="p-2 rounded-lg text-surface-400 hover:text-primary-400 hover:bg-primary-500/10 transition-all" title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('contacts.edit', $contact) }}" class="p-2 rounded-lg text-surface-400 hover:text-warning-400 hover:bg-warning-500/10 transition-all" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <a href="{{ route('contacts.send-email', $contact) }}" class="p-2 rounded-lg text-surface-400 hover:text-accent-400 hover:bg-accent-500/10 transition-all" title="Send Email">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </a>
                                        <form id="delete-contact-{{ $contact->id }}" method="POST" action="{{ route('contacts.destroy', $contact) }}" class="inline">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button onclick="confirmDelete('delete-contact-{{ $contact->id }}', 'contact')" class="p-2 rounded-lg text-surface-400 hover:text-danger-400 hover:bg-danger-500/10 transition-all" title="Delete">
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
                {{ $contacts->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-surface-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <h3 class="text-lg font-semibold text-surface-300 mb-2">No contacts found</h3>
                <p class="text-sm text-surface-500 mb-6">Get started by adding your first visiting card contact.</p>
                <a href="{{ route('contacts.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-400 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Contact
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
