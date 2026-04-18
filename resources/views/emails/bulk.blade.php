@extends('layouts.app')
@section('title', 'Send Bulk Email')
@section('subtitle', 'To ' . $contacts->count() . ' selected contacts')

@section('content')
    <div class="max-w-3xl mx-auto animate-fade-in">
        <form method="POST" action="{{ route('contacts.bulk-email.store') }}">
            @csrf
            <input type="hidden" name="contact_ids" value="{{ $contacts->pluck('id')->join(',') }}">

            <!-- Step 1: Selected Contacts Summary -->
            <div class="glass rounded-2xl p-4 sm:p-6 mb-6">
                <h3 class="text-base sm:text-lg font-semibold text-white mb-1">Step 1 — Recipients</h3>
                <p class="text-xs sm:text-sm text-surface-400 mb-5">You have selected {{ $contacts->count() }} contacts. Emails will be sent to all active email addresses for these contacts.</p>
                
                <div class="max-h-[150px] sm:max-h-[200px] overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                    @foreach($contacts as $contact)
                        <div class="flex flex-col xs:flex-row xs:items-center justify-between p-2 rounded-lg bg-surface-800/30 border border-surface-700/30 gap-1 sm:gap-0">
                            <span class="text-xs font-medium text-surface-200 truncate">{{ $contact->name }}</span>
                            <span class="text-[10px] text-surface-500 whitespace-nowrap">{{ $contact->emails->count() }} email(s)</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Step 2: Select Configuration -->
            <div class="glass rounded-2xl p-4 sm:p-6 mb-6">
                <h3 class="text-base sm:text-lg font-semibold text-white mb-1">Step 2 — Select Email Configuration</h3>
                <p class="text-xs sm:text-sm text-surface-400 mb-5">Choose the sender</p>
                @if ($configurations->count() > 0)
                    <select name="email_configuration_id" id="config-select" required
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-3 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                        <option value="">Select configuration...</option>
                        @foreach ($configurations as $config)
                            <option value="{{ $config->id }}" {{ old('email_configuration_id') == $config->id ? 'selected' : '' }}>
                                {{ $config->from_name }} &lt;{{ $config->from_email }}&gt; ({{ $config->name }})
                            </option>
                        @endforeach
                    </select>
                @else
                    <p class="text-sm text-danger-400">No active email configurations found.</p>
                @endif
                @error('email_configuration_id')
                    <p class="mt-2 text-sm text-danger-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Step 3: Select Template -->
            <div class="glass rounded-2xl p-4 sm:p-6 mb-6">
                <h3 class="text-base sm:text-lg font-semibold text-white mb-1">Step 3 — Select Template</h3>
                <p class="text-xs sm:text-sm text-surface-400 mb-5">Choose an email template</p>
                @if ($templates->count() > 0)
                    <select name="email_template_id" id="template-select" required
                        onchange="loadBulkPreview(this.value, '{{ $contacts->first()->id }}', document.getElementById('config-select').value)"
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-3 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                        <option value="">Select template...</option>
                        @foreach ($templates as $template)
                            <option value="{{ $template->id }}" {{ old('email_template_id') == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                        @endforeach
                    </select>
                    <!-- Preview (using the first contact as sample) -->
                    <div id="template-preview" class="mt-4 p-4 rounded-xl bg-surface-800 border border-surface-700 hidden">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-surface-500 mb-2">Preview (Sample: {{ $contacts->first()->name }})</p>
                        <p class="text-sm font-medium text-surface-200 mb-2">Subject: <span id="preview-subject" class="text-primary-400"></span></p>
                        <iframe id="preview-body" class="w-full mt-2 bg-transparent rounded-lg border border-surface-700"
                            style="height: 400px;" srcdoc=""></iframe>
                    </div>
                @else
                    <p class="text-sm text-danger-400">No active templates found.</p>
                @endif
                @error('email_template_id')
                    <p class="mt-2 text-sm text-danger-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Step 4: Attachments -->
            <div class="glass rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-1">Step 4 — Attachments</h3>
                <p class="text-sm text-surface-400 mb-5">Include card images if available for each contact</p>
                <div class="space-y-3">
                    @foreach (['front' => 'Card Front Image', 'back' => 'Card Back Image', 'other' => 'Other Image'] as $type => $label)
                        <label class="flex items-center gap-4 p-3 rounded-xl bg-surface-800/50 border border-surface-700/50 hover:border-primary-500/30 cursor-pointer transition-all">
                            <input type="checkbox" name="attach_{{ $type }}" value="1" checked
                                class="rounded border-surface-600 bg-surface-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0" />
                            <span class="text-sm text-surface-300">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Step 5: Send -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('contacts.index') }}"
                    class="order-2 sm:order-1 px-5 py-2.5 rounded-xl text-sm font-medium text-surface-400 hover:text-surface-200 transition-colors">← Back to Contacts</a>
                <button type="submit"
                    class="order-1 sm:order-2 w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20 hover:shadow-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Queue Bulk Emails
                </button>
            </div>
        </form>
    </div>

@push('scripts')
    <script>
        function loadBulkPreview(templateId, contactId, configId) {
            if (!templateId || !contactId) {
                document.getElementById('template-preview').classList.add('hidden');
                return;
            }

            document.getElementById('template-preview').classList.remove('hidden');

            fetch('{{ route('email-preview') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    template_id: templateId,
                    contact_id: contactId,
                    config_id: configId
                })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('preview-subject').textContent = data.subject || '(No subject)';
                document.getElementById('preview-body').srcdoc = data.body || '';
            });
        }

        // Initialize preview if template is already selected (e.g. from old input)
        document.addEventListener('DOMContentLoaded', function() {
            const templateSelect = document.getElementById('template-select');
            if (templateSelect.value) {
                loadBulkPreview(templateSelect.value, '{{ $contacts->first()->id }}', document.getElementById('config-select').value);
            }
            
            document.getElementById('config-select').addEventListener('change', function() {
                if (templateSelect.value) {
                    loadBulkPreview(templateSelect.value, '{{ $contacts->first()->id }}', this.value);
                }
            });
        });
    </script>
@endpush
@endsection
