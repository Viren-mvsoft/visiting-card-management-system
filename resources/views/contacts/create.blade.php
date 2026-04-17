@extends('layouts.app')
@section('title', 'Add Contact')
@section('subtitle', 'Create a new visiting card entry')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in">
    <form method="POST" action="{{ route('contacts.store') }}" enctype="multipart/form-data" autocomplete="off">
        @csrf

        <!-- Basic Information -->
        <div class="glass rounded-2xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-white mb-5">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Full Name <span class="text-danger-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all"
                        placeholder="John Doe" />
                    @error('name') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}"
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all"
                        placeholder="Acme Inc." />
                    @error('company_name') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Country</label>
                    <input type="text" name="country" value="{{ old('country') }}"
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all"
                        placeholder="India" />
                    @error('country') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Event</label>
                    <select id="event_id" name="event_id" placeholder="Select or type to create an event..."
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                        <option value=""></option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                        @endforeach
                    </select>
                    @error('event_id') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-surface-300 mb-2">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all resize-none"
                        placeholder="Optional notes about this contact...">{{ old('notes') }}</textarea>
                    @error('notes') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Phone Numbers -->
        <div class="glass rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-semibold text-white">Phone Numbers</h3>
                <button type="button" onclick="addRepeatableField('phone-fields', 'phone')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-primary-400 hover:bg-primary-500/10 border border-primary-500/30 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Phone
                </button>
            </div>
            <div id="phone-fields" class="space-y-3">
                <div class="flex items-center gap-2 sm:gap-3" id="phones-row-0">
                    <select name="phones[0][label]" class="w-24 sm:w-32 shrink-0 rounded-xl border border-surface-700 bg-surface-800 px-3 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                        <option value="mobile">Mobile</option>
                        <option value="office">Office</option>
                        <option value="other">Other</option>
                    </select>
                    <div class="flex-1">
                        <input type="tel" name="phones[0][phone]" placeholder="Phone number"
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Addresses -->
        <div class="glass rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-semibold text-white">Email Addresses</h3>
                <button type="button" onclick="addRepeatableField('email-fields', 'email')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-primary-400 hover:bg-primary-500/10 border border-primary-500/30 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Email
                </button>
            </div>
            <div id="email-fields" class="space-y-3">
                <div class="flex items-center gap-2 sm:gap-3">
                    <select name="emails[0][label]" class="w-24 sm:w-32 shrink-0 rounded-xl border border-surface-700 bg-surface-800 px-3 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                        <option value="work">Work</option>
                        <option value="personal">Personal</option>
                        <option value="other">Other</option>
                    </select>
                    <input type="email" name="emails[0][email]" placeholder="Email address"
                        class="flex-1 min-w-0 rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                </div>
            </div>
        </div>

        <!-- Card Images -->
        <div class="glass rounded-2xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-white mb-5">Card Images</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach(['card_front' => 'Front', 'card_back' => 'Back', 'card_other' => 'Other'] as $name => $label)
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">{{ $label }} Image</label>
                        <div class="relative rounded-xl border-2 border-dashed border-surface-700 hover:border-primary-500/50 transition-colors p-4 text-center cursor-pointer" onclick="this.querySelector('input[type=file]').click()">
                            <img id="preview-{{ $name }}" src="" class="hidden w-full h-40 object-cover rounded-lg mb-3" />
                            <div class="upload-placeholder">
                                <svg class="w-8 h-8 mx-auto text-surface-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-xs text-surface-500">Click to upload</p>
                                <p class="text-xs text-surface-600 mt-1">JPG, PNG, WEBP · Max 5MB</p>
                            </div>
                            <input type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewImage(this, 'preview-{{ $name }}')" />
                        </div>
                        @error($name) <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('contacts.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-surface-400 hover:text-surface-200 transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20">
                Create Contact
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tom Select for events
    new TomSelect('#event_id', {
        create: true,
        sortField: {
            field: "text",
            direction: "asc"
        },
        placeholder: "Select or type to create an event...",
        maxOptions: 50,
        render: {
            option_create: function(data, escape) {
                return '<div class="create">Add <strong>' + escape(data.input) + '</strong>...</div>';
            }
        }
    });

    // Initialize default phone input
    const initialPhone = document.querySelector('#phones-row-0 input[type="tel"]');
    if (initialPhone) {
        window.initPhoneInput(initialPhone);
    }

    // Capture full numbers on submit
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        window.syncPhoneNumbers(form);
    });
});
</script>
@endpush
