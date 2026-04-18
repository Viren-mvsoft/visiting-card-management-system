@extends('layouts.app')
@section('title', 'Edit Contact')
@section('subtitle', 'Update ' . $contact->name)

@section('content')
    <div class="max-w-4xl mx-auto animate-fade-in">
        <form method="POST" action="{{ route('contacts.update', $contact) }}" enctype="multipart/form-data" autocomplete="off">
            @csrf @method('PUT')

            <!-- Basic Information -->
            <div class="glass rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-5">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Full Name <span
                                class="text-danger-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $contact->name) }}" required
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                        @error('name')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $contact->company_name) }}"
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Country</label>
                        <select id="country_id" name="country_id" placeholder="Select a country..."
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                            <option value=""></option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}"
                                    {{ old('country_id', $contact->country_id) == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('country_id')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Event</label>
                        <select id="event_id" name="event_id" placeholder="Select or type to create an event..."
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                            <option value=""></option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}"
                                    {{ old('event_id', $contact->event_id) == $event->id ? 'selected' : '' }}>
                                    {{ $event->name }}</option>
                            @endforeach
                        </select>
                        @error('event_id')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Website</label>
                        <input type="url" name="website" value="{{ old('website', $contact->website) }}"
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all"
                            placeholder="https://example.com" />
                        @error('website')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-surface-300 mb-2">Physical Address</label>
                        <textarea name="address" rows="2"
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all resize-none"
                            placeholder="Full office or home address...">{{ old('address', $contact->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-surface-300 mb-2">Notes</label>
                        <textarea name="notes" rows="3"
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all resize-none">{{ old('notes', $contact->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Phone Numbers -->
            <div class="glass rounded-2xl p-6 mb-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-white">Phone Numbers</h3>
                    <button type="button" onclick="addRepeatableField('phone-fields', 'phone')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-primary-400 hover:bg-primary-500/10 border border-primary-500/30 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Phone
                    </button>
                </div>
                <div id="phone-fields" class="space-y-3">
                    @forelse($contact->phones as $i => $phone)
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3" id="phones-row-{{ $i }}">
                            <select name="phones[{{ $i }}][label]"
                                class="w-full sm:w-32 shrink-0 rounded-xl border border-surface-700 bg-surface-800 px-3 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all order-1">
                                <option value="mobile" {{ $phone->label === 'mobile' ? 'selected' : '' }}>Mobile</option>
                                <option value="office" {{ $phone->label === 'office' ? 'selected' : '' }}>Office</option>
                                <option value="other" {{ $phone->label === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <div class="flex-1 min-w-[200px] order-2">
                                <input type="tel" name="phones[{{ $i }}][phone]"
                                    value="{{ $phone->phone }}"
                                    class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                            </div>
                            <button type="button" onclick="this.parentElement.remove()"
                                class="shrink-0 p-2 rounded-lg text-danger-400 hover:bg-danger-500/10 transition-colors order-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    @empty
                        <div class="flex items-center gap-2 sm:gap-3" id="phones-row-0">
                            <select name="phones[0][label]"
                                class="w-24 sm:w-32 shrink-0 rounded-xl border border-surface-700 bg-surface-800 px-3 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                                <option value="mobile">Mobile</option>
                                <option value="office">Office</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="flex-1">
                                <input type="tel" name="phones[0][phone]" placeholder="Phone number"
                                    class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Email Addresses -->
            <div class="glass rounded-2xl p-6 mb-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-white">Email Addresses</h3>
                    <button type="button" onclick="addRepeatableField('email-fields', 'email')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-primary-400 hover:bg-primary-500/10 border border-primary-500/30 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Email
                    </button>
                </div>
                <div id="email-fields" class="space-y-3">
                    @forelse($contact->emails as $i => $email)
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                            <select name="emails[{{ $i }}][label]"
                                class="w-full sm:w-32 shrink-0 rounded-xl border border-surface-700 bg-surface-800 px-3 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all order-1">
                                <option value="work" {{ $email->label === 'work' ? 'selected' : '' }}>Work</option>
                                <option value="personal" {{ $email->label === 'personal' ? 'selected' : '' }}>Personal
                                </option>
                                <option value="other" {{ $email->label === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <div class="flex-1 min-w-[200px] order-2">
                                <input type="email" name="emails[{{ $i }}][email]"
                                    value="{{ $email->email }}"
                                    class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                            </div>
                            <button type="button" onclick="this.parentElement.remove()"
                                class="shrink-0 p-2 rounded-lg text-danger-400 hover:bg-danger-500/10 transition-colors order-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    @empty
                        <div class="flex items-center gap-2 sm:gap-3">
                            <select name="emails[0][label]"
                                class="w-24 sm:w-32 shrink-0 rounded-xl border border-surface-700 bg-surface-800 px-3 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                                <option value="work">Work</option>
                                <option value="personal">Personal</option>
                                <option value="other">Other</option>
                            </select>
                            <input type="email" name="emails[0][email]" placeholder="Email address"
                                class="flex-1 min-w-0 rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Card Images -->
            <div class="glass rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-5">Card Images</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach (['card_front' => ['Front', 'front'], 'card_back' => ['Back', 'back'], 'card_other' => ['Other', 'other']] as $name => [$label, $type])
                        @php $existingImage = $contact->images->where('type', $type)->first(); @endphp
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">{{ $label }}
                                Image</label>
                            @if ($existingImage)
                                <div class="relative rounded-xl overflow-hidden mb-2 group">
                                    <img src="{{ Storage::url($existingImage->file_path) }}"
                                        class="w-full h-40 object-cover cursor-pointer"
                                        onclick="openLightbox(this.src)" />
                                    <button type="button"
                                        onclick="confirmDelete('delete-image-{{ $existingImage->id }}', 'image')"
                                        class="absolute top-2 right-2 p-1.5 rounded-lg bg-danger-500/80 text-white hover:bg-danger-500 transition-colors opacity-0 group-hover:opacity-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                            <div class="relative rounded-xl border-2 border-dashed border-surface-700 hover:border-primary-500/50 transition-colors p-4 text-center cursor-pointer"
                                onclick="this.querySelector('input[type=file]').click()">
                                <img id="preview-{{ $name }}" src=""
                                    class="hidden w-full h-32 object-cover rounded-lg mb-2" />
                                <div class="upload-placeholder">
                                    <svg class="w-6 h-6 mx-auto text-surface-600 mb-1" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-xs text-surface-500">
                                        {{ $existingImage ? 'Replace image' : 'Click to upload' }}</p>
                                </div>
                                <input type="file" name="{{ $name }}"
                                    accept="image/jpeg,image/png,image/webp" class="hidden"
                                    onchange="previewImage(this, 'preview-{{ $name }}')" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('contacts.show', $contact) }}"
                    class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl text-sm font-medium text-surface-400 hover:text-surface-200 transition-colors order-3 sm:order-1">Cancel</a>

                <button type="button" onclick="saveToPhone()"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl border border-surface-700 bg-surface-800 text-surface-200 text-sm font-semibold hover:bg-surface-700 transition-all shadow-lg shadow-black/20 order-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Save to Phone
                </button>

                <button type="submit"
                    class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20 order-1 sm:order-3">
                    Update Contact
                </button>
            </div>
        </form>

        <!-- Hidden forms for image deletion -->
        @foreach (['front', 'back', 'other'] as $type)
            @php $existingImage = $contact->images->where('type', $type)->first(); @endphp
            @if ($existingImage)
                <form id="delete-image-{{ $existingImage->id }}" method="POST"
                    action="{{ route('contacts.delete-image', [$contact, $existingImage]) }}" class="hidden">
                    @csrf @method('DELETE')
                </form>
            @endif
        @endforeach
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tom Select for countries
            new TomSelect('#country_id', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "Select a country...",
                maxOptions: 250
            });

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
                        return '<div class="create">Add <strong>' + escape(data.input) +
                            '</strong>...</div>';
                    }
                }
            });

            // Initialize all existing phone inputs
            const phoneInputs = document.querySelectorAll('#phone-fields input[type="tel"]');
            phoneInputs.forEach(input => {
                window.initPhoneInput(input);
            });

            // Capture full numbers on submit
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                window.syncPhoneNumbers(form);
            });
        });

        function saveToPhone() {
            const form = document.querySelector('form');
            const originalAction = form.action;
            const originalTarget = form.target;
            const originalMethod = form.method;

            // Use vCard preview route
            form.action = '{{ route('contacts.vcard-preview') }}';
            form.method = 'POST';
            form.target = '_blank';
            form.submit();

            // Restore original form state
            form.action = originalAction;
            form.method = originalMethod;
            form.target = originalTarget;
        }
    </script>
@endpush
