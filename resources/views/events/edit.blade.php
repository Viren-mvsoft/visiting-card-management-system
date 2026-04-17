@extends('layouts.app')
@section('title', 'Edit Event')
@section('subtitle', 'Update event details')

@section('content')
<div class="max-w-2xl mx-auto animate-fade-in">
    <form method="POST" action="{{ route('events.update', $event) }}" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="glass rounded-2xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-white mb-5">Event Details</h3>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Event Name <span class="text-danger-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $event->name) }}" required
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all"
                        placeholder="e.g. CES 2026, Networking Mixer" />
                    @error('name') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Event Date</label>
                        <input type="date" name="event_date" value="{{ old('event_date', $event->event_date) }}"
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                        @error('event_date') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Location</label>
                        <input type="text" name="location" value="{{ old('location', $event->location) }}"
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all"
                            placeholder="Las Vegas, NV" />
                        @error('location') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all resize-none"
                        placeholder="Optional details about this event...">{{ old('description', $event->description) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('events.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-surface-400 hover:text-surface-200 transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20">
                Update Event
            </button>
        </div>
    </form>
</div>
@endsection
