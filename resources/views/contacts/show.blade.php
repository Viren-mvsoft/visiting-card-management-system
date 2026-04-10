@extends('layouts.app')
@section('title', $contact->name)
@section('subtitle', $contact->company_name ?? 'Contact Details')

@section('header-actions')
<div class="flex items-center gap-3">
    <a href="{{ route('contacts.send-email', $contact) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-accent-500 to-accent-600 text-white text-sm font-semibold hover:from-accent-400 hover:to-accent-500 transition-all shadow-lg shadow-accent-500/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        Send Email
    </a>
    <a href="{{ route('contacts.edit', $contact) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-800 text-surface-300 text-sm font-medium hover:bg-surface-700 border border-surface-700 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Details -->
            <div class="glass rounded-2xl p-6">
                <div class="flex items-start gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-primary-500/20">
                        {{ strtoupper(substr($contact->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-white">{{ $contact->name }}</h2>
                        @if($contact->company_name)
                            <p class="text-surface-400 mt-1">{{ $contact->company_name }}</p>
                        @endif
                        <div class="flex flex-wrap items-center gap-3 mt-3">
                            @if($contact->country)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-medium bg-surface-800 text-surface-300 border border-surface-700">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                                    {{ $contact->country }}
                                </span>
                            @endif
                            @if($contact->event)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-medium bg-primary-500/10 text-primary-400 border border-primary-500/20">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $contact->event }}
                                </span>
                            @endif
                            <span class="text-xs text-surface-500">Added {{ $contact->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                @if($contact->notes)
                    <div class="mt-5 pt-5 border-t border-surface-700/50">
                        <p class="text-sm text-surface-400 leading-relaxed">{{ $contact->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Phone & Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="glass rounded-2xl p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-surface-400 mb-4">Phone Numbers</h3>
                    @forelse($contact->phones as $phone)
                        <div class="flex items-center gap-3 py-2">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-surface-800 text-surface-400 capitalize">{{ $phone->label }}</span>
                            <span class="text-sm text-surface-200">{{ $phone->phone }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-surface-500">No phone numbers</p>
                    @endforelse
                </div>
                <div class="glass rounded-2xl p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-surface-400 mb-4">Email Addresses</h3>
                    @forelse($contact->emails as $email)
                        <div class="flex items-center gap-3 py-2">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-surface-800 text-surface-400 capitalize">{{ $email->label }}</span>
                            <span class="text-sm text-surface-200">{{ $email->email }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-surface-500">No email addresses</p>
                    @endforelse
                </div>
            </div>

            <!-- Email History -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-5">Email History</h3>
                @forelse($contact->emailLogs as $log)
                    <div class="flex items-start gap-4 py-4 {{ !$loop->last ? 'border-b border-surface-800/50' : '' }}">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $log->status === 'sent' ? 'bg-success-500/15 text-success-400' : ($log->status === 'failed' ? 'bg-danger-500/15 text-danger-400' : 'bg-warning-500/15 text-warning-400') }}">
                            @if($log->status === 'sent')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @elseif($log->status === 'failed')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-surface-200">{{ $log->subject }}</p>
                            <p class="text-xs text-surface-500 mt-1">To: {{ is_array($log->recipients) ? implode(', ', $log->recipients) : $log->recipients }}</p>
                            @if($log->template)
                                <p class="text-xs text-surface-500">Template: {{ $log->template->name }}</p>
                            @endif
                            @if($log->error)
                                <p class="text-xs text-danger-400 mt-1">Error: {{ $log->error }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-medium {{ $log->status === 'sent' ? 'bg-success-500/15 text-success-400' : ($log->status === 'failed' ? 'bg-danger-500/15 text-danger-400' : 'bg-warning-500/15 text-warning-400') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                            <p class="text-xs text-surface-500 mt-1">{{ ($log->sent_at ?? $log->created_at)->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-surface-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <p class="text-sm text-surface-500">No emails sent to this contact yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Sidebar: Card Images -->
        <div class="space-y-6">
            <div class="glass rounded-2xl p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-surface-400 mb-4">Card Images</h3>
                <div class="space-y-4">
                    @foreach(['front' => 'Front', 'back' => 'Back', 'other' => 'Other'] as $type => $label)
                        @php $img = $contact->images->where('type', $type)->first(); @endphp
                        <div>
                            <p class="text-xs text-surface-500 mb-2">{{ $label }}</p>
                            @if($img)
                                <img src="{{ Storage::url($img->file_path) }}" alt="{{ $label }} Card"
                                     class="w-full rounded-xl cursor-pointer hover:ring-2 hover:ring-primary-500/50 transition-all shadow-lg"
                                     onclick="openLightbox(this.src)" />
                            @else
                                <div class="w-full h-32 rounded-xl bg-surface-800 border border-surface-700 flex items-center justify-center">
                                    <span class="text-xs text-surface-500">No image</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            @if(auth()->user()->isAdmin() && $contact->user)
                <div class="glass rounded-2xl p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-surface-400 mb-4">Created By</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-primary-500/20 to-accent-500/20 flex items-center justify-center text-primary-400 font-semibold text-xs border border-primary-500/20">
                            {{ strtoupper(substr($contact->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-surface-200">{{ $contact->user->name }}</p>
                            <p class="text-xs text-surface-500 capitalize">{{ $contact->user->role }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
