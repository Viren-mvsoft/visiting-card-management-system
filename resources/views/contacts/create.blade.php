@extends('layouts.app')
@section('title', 'Add Contact')
@section('subtitle', 'Create a new visiting card entry')

@section('content')
    <div class="max-w-4xl mx-auto animate-fade-in">
        <form method="POST" action="{{ route('contacts.store') }}" enctype="multipart/form-data" autocomplete="off">
            @csrf

            <!-- Basic Information -->
            <div class="glass rounded-2xl p-6 mb-6">
                <div class="flex flex-col gap-4 mb-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">Basic Information</h3>
                        <div class="flex items-center gap-2">
                            <input type="file" id="scan-input" multiple accept="image/*,.pdf" class="hidden"
                                onchange="addToQueue(this)">
                            <button type="button" onclick="document.getElementById('scan-input').click()" id="scan-btn"
                                class="inline-flex items-center gap-2 px-5 py-2 rounded-xl border border-surface-700 bg-surface-800 text-surface-200 text-sm font-semibold hover:bg-surface-700 transition-all shadow-lg shadow-black/20">
                                <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Add Image for Scan
                            </button>
                        </div>
                    </div>

                    <!-- Scan Queue UI -->
                    <div id="scan-queue" class="hidden">
                        <div
                            class="flex flex-wrap items-center gap-4 p-3 rounded-2xl bg-surface-900/50 border border-primary-500/20">
                            <div id="queue-thumbnails" class="flex flex-wrap gap-2"></div>
                            <div class="h-8 w-px bg-surface-700 hidden sm:block"></div>
                            <div class="flex items-center gap-3 ml-auto">
                                <button type="button" onclick="clearQueue()"
                                    class="text-xs text-surface-500 hover:text-danger-400 font-medium transition-colors">Clear
                                    All</button>
                                <button type="button" onclick="processQueue()" id="process-btn"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-500 text-white text-xs font-bold uppercase tracking-wider hover:bg-primary-400 transition-all shadow-lg shadow-primary-500/20">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Scan <span id="queue-count">0</span> Image(s)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Full Name <span
                                class="text-danger-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all"
                            placeholder="John Doe" />
                        @error('name')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}"
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all"
                            placeholder="Acme Inc." />
                        @error('company_name')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Country</label>
                        <select id="country_id" name="country_id" placeholder="Select a country..."
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                            <option value=""></option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}"
                                    {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
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
                                <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->name }}</option>
                            @endforeach
                        </select>
                        @error('event_id')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Website</label>
                        <input type="url" name="website" value="{{ old('website') }}"
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
                            placeholder="Full office or home address...">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-surface-300 mb-2">Notes</label>
                        <textarea name="notes" rows="3"
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all resize-none"
                            placeholder="Optional notes about this contact...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                        @enderror
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
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3" id="phones-row-0">
                        <select name="phones[0][label]"
                            class="w-full sm:w-32 shrink-0 rounded-xl border border-surface-700 bg-surface-800 px-3 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all order-1">
                            <option value="mobile">Mobile</option>
                            <option value="office">Office</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="flex-1 min-w-[200px] order-2">
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
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Email
                    </button>
                </div>
                <div id="email-fields" class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <select name="emails[0][label]"
                            class="w-full sm:w-32 shrink-0 rounded-xl border border-surface-700 bg-surface-800 px-3 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all order-1">
                            <option value="work">Work</option>
                            <option value="personal">Personal</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="flex-1 min-w-[200px] order-2">
                            <input type="email" name="emails[0][email]" placeholder="Email address"
                                class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Images -->
            <div class="glass rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-5">Card Images</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach (['card_front' => 'Front', 'card_back' => 'Back', 'card_other' => 'Other'] as $name => $label)
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">{{ $label }}
                                Image</label>
                            <div class="relative rounded-xl border-2 border-dashed border-surface-700 hover:border-primary-500/50 transition-colors p-4 text-center cursor-pointer"
                                onclick="this.querySelector('input[type=file]').click()">
                                <img id="preview-{{ $name }}" src=""
                                    class="hidden w-full h-40 object-cover rounded-lg mb-3" />
                                <div class="upload-placeholder">
                                    <svg class="w-8 h-8 mx-auto text-surface-600 mb-2" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-xs text-surface-500">Click to upload</p>
                                    <p class="text-xs text-surface-600 mt-1">JPG, PNG, WEBP · Max 5MB</p>
                                </div>
                                <input type="file" name="{{ $name }}"
                                    accept="image/jpeg,image/png,image/webp" class="hidden"
                                    onchange="previewImage(this, 'preview-{{ $name }}')" />
                            </div>
                            @error($name)
                                <p class="mt-1 text-sm text-danger-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('contacts.index') }}"
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
                    Create Contact
                </button>
            </div>
        </form>
    </div>

    <!-- Scanning Overlay -->
    <div id="scan-overlay"
        class="fixed inset-0 z-50 flex items-center justify-center bg-surface-950/80 backdrop-blur-sm hidden">
        <div class="text-center">
            <div class="relative w-24 h-24 mx-auto mb-6">
                <div class="absolute inset-0 rounded-full border-4 border-primary-500/20"></div>
                <div class="absolute inset-0 rounded-full border-4 border-primary-500 border-t-transparent animate-spin">
                </div>
                <div
                    class="absolute inset-4 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 animate-pulse flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
            <h2 class="text-xl font-bold text-white mb-2">Analyzing Business Card...</h2>
            <p class="text-surface-400 text-sm animate-pulse">Our AI is extracting contact details</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tom Select for countries
            window.countrySelect = new TomSelect('#country_id', {
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

        let scanQueue = [];

        function addToQueue(input) {
            if (!input.files || input.files.length === 0) return;

            for (let i = 0; i < input.files.length; i++) {
                scanQueue.push(input.files[i]);
            }

            renderQueue();
            input.value = ''; // Reset for next selection
        }

        function clearQueue() {
            scanQueue = [];
            renderQueue();
        }

        function renderQueue() {
            const queueSection = document.getElementById('scan-queue');
            const thumbnailContainer = document.getElementById('queue-thumbnails');
            const countDisplay = document.getElementById('queue-count');

            if (scanQueue.length === 0) {
                queueSection.classList.add('hidden');
                return;
            }

            queueSection.classList.remove('hidden');
            countDisplay.innerText = scanQueue.length;
            thumbnailContainer.innerHTML = '';

            scanQueue.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const thumb = document.createElement('div');
                    thumb.className =
                        'relative group w-12 h-12 rounded-lg bg-surface-800 border border-surface-700 overflow-hidden';
                    thumb.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <button type="button" onclick="removeFromQueue(${index})" class="absolute inset-0 flex items-center justify-center bg-danger-500/80 opacity-0 group-hover:opacity-100 transition-all text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    `;
                    thumbnailContainer.appendChild(thumb);
                };
                reader.readAsDataURL(file);
            });
        }

        function removeFromQueue(index) {
            scanQueue.splice(index, 1);
            renderQueue();
        }

        async function processQueue() {
            if (scanQueue.length === 0) return;

            const overlay = document.getElementById('scan-overlay');
            overlay.classList.remove('hidden');

            const formData = new FormData();
            scanQueue.forEach(file => {
                formData.append('files[]', file);
            });

            try {
                const response = await fetch('{{ route('contacts.scan') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    fillForm(result.data);
                    showNotification('Card scanned successfully!', 'success');
                    clearQueue();
                } else {
                    showNotification(result.message || 'Scanning failed', 'error');
                }
            } catch (error) {
                console.error(error);
                showNotification('Something went wrong during scanning', 'error');
            } finally {
                overlay.classList.add('hidden');
            }
        }

        function fillForm(data) {
            if (data.name) document.querySelector('input[name="name"]').value = data.name;
            if (data.company) document.querySelector('input[name="company_name"]').value = data.company;
            if (data.website) document.querySelector('input[name="website"]').value = data.website;
            if (data.address) document.querySelector('textarea[name="address"]').value = data.address;
            if (data.notes) document.querySelector('textarea[name="notes"]').value = data.notes;

            // Handle Country (TomSelect)
            if (data.country && window.countrySelect) {
                const countryValue = data.country.trim().toLowerCase();
                const options = window.countrySelect.options;

                let matchedId = null;

                // 1. Try exact match first
                for (const id in options) {
                    if (options[id].text.toLowerCase() === countryValue) {
                        matchedId = id;
                        break;
                    }
                }

                // 2. Try partial match only if no exact match found
                if (!matchedId) {
                    for (const id in options) {
                        const optText = options[id].text.toLowerCase();
                        if (optText.includes(countryValue) || countryValue.includes(optText)) {
                            matchedId = id;
                            break;
                        }
                    }
                }

                if (matchedId) {
                    window.countrySelect.setValue(matchedId);
                }
            }

            // Handle Phone Numbers
            if (data.phones && data.phones.length > 0) {
                const phoneContainer = document.getElementById('phone-fields');
                phoneContainer.innerHTML = ''; // Clear existing
                data.phones.forEach((phone, index) => {
                    addRepeatableField('phone-fields', 'phone');
                    const row = phoneContainer.lastElementChild;
                    const input = row.querySelector('input[type="tel"]');
                    input.value = phone;
                    // Initialize intl-tel-input
                    if (window.initPhoneInput) window.initPhoneInput(input);
                });
            }

            // Handle Email Addresses
            if (data.emails && data.emails.length > 0) {
                const emailContainer = document.getElementById('email-fields');
                emailContainer.innerHTML = ''; // Clear existing
                data.emails.forEach((email, index) => {
                    addRepeatableField('email-fields', 'email');
                    const row = emailContainer.lastElementChild;
                    const input = row.querySelector('input[type="email"]');
                    input.value = email;
                });
            }
        }

        function showNotification(message, type = 'success') {
            // Basic alert for now, can be replaced with a toast
            alert(message);
        }

        function saveToPhone() {
            const form = document.querySelector('form');
            const originalAction = form.action;
            const originalTarget = form.target;
            const originalMethod = form.method;

            // Submit to vCard preview route in a new window/tab for file download
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
