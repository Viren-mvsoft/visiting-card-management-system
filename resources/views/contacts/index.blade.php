@extends('layouts.app')
@section('title', 'Contacts')
@section('subtitle', auth()->user()->isAdmin() ? 'All visiting cards' : 'Your visiting cards')

@section('header-actions')
    <a href="{{ route('contacts.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20 hover:shadow-xl">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Contact
    </a>
@endsection

@section('content')
    <div class="space-y-6 animate-fade-in">
        <!-- Search & Filters -->
        <div class="glass rounded-2xl p-5">
            <form method="GET" action="{{ route('contacts.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 sm:flex sm:flex-row gap-3">
                    <div class="sm:flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search contacts..."
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                    </div>

                    <div class="grid grid-cols-2 sm:flex gap-3">
                        <select name="country_id"
                            class="w-full sm:w-auto rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                            <option value="">All Countries</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}"
                                    {{ request('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="event_id"
                            class="w-full sm:w-auto rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                            <option value="">All Events</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}"
                                    {{ request('event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 sm:flex gap-3">
                        <select name="per_page"
                            class="w-full sm:min-w-[100px] rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                            <option value="25" {{ request('per_page', '25') == '25' ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                            <option value="500" {{ request('per_page') == '500' ? 'selected' : '' }}>All</option>
                        </select>
                        <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-400 shadow-lg shadow-primary-500/20 transition-all">
                            Filter
                        </button>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('contacts.export', request()->all()) }}"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-surface-800 text-surface-300 text-sm font-medium hover:bg-surface-700 border border-surface-700 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export
                        </a>
                        @if (request()->hasAny(['search', 'country_id', 'event_id']))
                            <a href="{{ route('contacts.index') }}"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-surface-400 text-sm font-medium hover:text-surface-200 transition-all border border-transparent hover:border-surface-700">Clear</a>
                        @endif
                    </div>
                </div>
                @if ($contacts->total() > 0)
                    <div class="text-xs text-surface-500">
                        Showing {{ $contacts->firstItem() }}–{{ $contacts->lastItem() }} of {{ $contacts->total() }}
                        contacts
                    </div>
                @endif
            </form>
        </div>

        <!-- Contacts Table -->
        <div class="glass rounded-2xl overflow-hidden">
            @if ($contacts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-surface-700/50 text-nowrap">
                                <th class="px-4 py-4 w-10">
                                    <input type="checkbox" id="select-all"
                                        class="rounded border-surface-700 bg-surface-800 text-primary-500 focus:ring-primary-500 transition-all cursor-pointer">
                                </th>
                                <th
                                    class="text-left px-4 sm:px-6 py-4 text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-surface-400 min-w-[120px] sm:min-w-0">
                                    Name</th>
                                <th
                                    class="text-left px-4 sm:px-6 py-4 text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-surface-400 min-w-[120px] sm:min-w-0">
                                    Company</th>
                                <th
                                    class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden md:table-cell text-nowrap">
                                    Country</th>
                                <th
                                    class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden lg:table-cell text-nowrap">
                                    Event</th>
                                @if (auth()->user()->isAdmin())
                                    <th
                                        class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden xl:table-cell text-nowrap">
                                        Created By</th>
                                @endif
                                <th
                                    class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400 hidden sm:table-cell text-nowrap">
                                    Date</th>
                                <th
                                    class="text-right px-4 sm:px-6 py-4 text-xs font-semibold uppercase tracking-wider text-surface-400">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-800/50">
                            @foreach ($contacts as $contact)
                                <tr class="hover:bg-surface-800/30 transition-colors group">
                                    <td class="px-4 py-4">
                                        <input type="checkbox" name="selected_contacts[]" value="{{ $contact->id }}"
                                            class="contact-checkbox rounded border-surface-700 bg-surface-800 text-primary-500 focus:ring-primary-500 transition-all cursor-pointer">
                                    </td>
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="hidden xs:flex w-9 h-9 rounded-lg bg-gradient-to-br from-primary-500/20 to-accent-500/20 items-center justify-center text-primary-400 font-semibold text-xs border border-primary-500/20 shrink-0">
                                                {{ strtoupper(substr($contact->name, 0, 1)) }}
                                            </div>
                                            <a href="{{ route('contacts.show', $contact) }}"
                                                class="text-sm font-medium text-surface-200 hover:text-white transition-colors truncate max-w-[120px] sm:max-w-none block">{{ $contact->name }}</a>
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-surface-400">
                                        <span
                                            class="truncate max-w-[100px] sm:max-w-none block">{{ $contact->company_name ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-surface-400 hidden md:table-cell text-nowrap">
                                        {{ $contact->country->name ?? '—' }}</td>
                                    <td class="px-6 py-4 hidden lg:table-cell">
                                        @if ($contact->event)
                                            <a href="{{ route('events.show', $contact->event) }}"
                                                class="inline-flex px-2.5 py-1 rounded-lg text-xs font-medium bg-primary-500/10 text-primary-400 border border-primary-500/20 hover:bg-primary-500/20 transition-all text-nowrap">{{ $contact->event->name }}</a>
                                        @else
                                            <span class="text-sm text-surface-500">—</span>
                                        @endif
                                    </td>
                                    @if (auth()->user()->isAdmin())
                                        <td class="px-6 py-4 text-sm text-surface-400 hidden xl:table-cell text-nowrap">
                                            {{ $contact->user->name ?? 'N/A' }}</td>
                                    @endif
                                    <td class="px-6 py-4 text-sm text-surface-500 hidden sm:table-cell text-nowrap">
                                        {{ $contact->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex items-center justify-end gap-1 sm:gap-2">
                                            <a href="{{ route('contacts.show', $contact) }}"
                                                class="p-1.5 sm:p-2 rounded-lg text-surface-400 hover:text-primary-400 hover:bg-primary-500/10 transition-all"
                                                title="View">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('contacts.edit', $contact) }}"
                                                class="p-1.5 sm:p-2 rounded-lg text-surface-400 hover:text-warning-400 hover:bg-warning-500/10 transition-all"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('contacts.send-email', $contact) }}"
                                                class="p-1.5 sm:p-2 rounded-lg text-surface-400 hover:text-accent-400 hover:bg-accent-500/10 transition-all"
                                                title="Send Email">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </a>
                                            <form id="delete-contact-{{ $contact->id }}" method="POST"
                                                action="{{ route('contacts.destroy', $contact) }}" class="inline">
                                                @csrf @method('DELETE')
                                            </form>
                                            <button
                                                onclick="confirmDelete('delete-contact-{{ $contact->id }}', 'contact')"
                                                class="hidden xs:flex p-1.5 sm:p-2 rounded-lg text-surface-400 hover:text-danger-400 hover:bg-danger-500/10 transition-all"
                                                title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
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
                    <svg class="w-16 h-16 mx-auto text-surface-700 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-surface-300 mb-2">No contacts found</h3>
                    <p class="text-sm text-surface-500 mb-6">Get started by adding your first visiting card contact.</p>
                    <a href="{{ route('contacts.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-400 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Contact
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulk-actions-bar"
        class="fixed bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:-translate-x-1/2 z-50 transform translate-y-32 opacity-0 transition-all duration-300">
        <div
            class="glass border border-surface-700 shadow-2xl rounded-2xl px-4 py-3 sm:px-6 sm:py-4 flex flex-col sm:flex-row items-center gap-3 sm:gap-6">
            <div class="text-sm font-medium text-surface-200">
                <span id="selected-count">0</span> contacts selected
            </div>
            <div class="hidden sm:block h-6 w-px bg-surface-700"></div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button type="button" onclick="sendBulkEmail()"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-primary-500 text-white text-xs sm:text-sm font-semibold hover:bg-primary-400 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Send Bulk Email
                </button>
                <button type="button" onclick="clearSelection()"
                    class="flex-1 sm:flex-none text-xs sm:text-sm font-medium text-surface-400 hover:text-surface-200 transition-colors text-center">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <form id="bulk-email-form" action="{{ route('contacts.bulk-email.create') }}" method="GET" class="hidden">
        <input type="hidden" name="contact_ids" id="bulk-contact-ids">
    </form>

    @push('scripts')
        <script>
            const selectAll = document.getElementById('select-all');
            const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
            const bulkActionsBar = document.getElementById('bulk-actions-bar');
            const selectedCount = document.getElementById('selected-count');
            const bulkContactIds = document.getElementById('bulk-contact-ids');
            const bulkEmailForm = document.getElementById('bulk-email-form');

            function updateBulkActions() {
                const checkedCount = Array.from(contactCheckboxes).filter(cb => cb.checked).length;
                selectedCount.textContent = checkedCount;

                if (checkedCount > 0) {
                    bulkActionsBar.classList.remove('translate-y-32', 'opacity-0');
                } else {
                    bulkActionsBar.classList.add('translate-y-32', 'opacity-0');
                }
            }

            selectAll.addEventListener('change', () => {
                contactCheckboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                updateBulkActions();
            });

            contactCheckboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    const allChecked = Array.from(contactCheckboxes).every(cb => cb.checked);
                    selectAll.checked = allChecked;
                    updateBulkActions();
                });
            });

            function clearSelection() {
                selectAll.checked = false;
                contactCheckboxes.forEach(cb => cb.checked = false);
                updateBulkActions();
            }

            function sendBulkEmail() {
                const ids = Array.from(contactCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                bulkContactIds.value = ids.join(',');
                bulkEmailForm.submit();
            }
        </script>
    @endpush
@endsection
