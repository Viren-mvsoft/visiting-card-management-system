@extends('layouts.app')
@section('title', 'Send Email')
@section('subtitle', 'To: ' . $contact->name)

@section('content')
    <div class="max-w-3xl mx-auto animate-fade-in">
        <form method="POST" action="{{ route('contacts.send-email.store', $contact) }}">
            @csrf

            <!-- Step 1: Select Recipients -->
            <div class="glass rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-1">Step 1 — Select Recipients</h3>
                <p class="text-sm text-surface-400 mb-5">Choose which email addresses to send to</p>
                @if ($contact->emails->count() > 0)
                    <div class="space-y-3">
                        @foreach ($contact->emails as $email)
                            <label
                                class="flex items-center gap-3 p-3 rounded-xl bg-surface-800/50 border border-surface-700/50 hover:border-primary-500/30 cursor-pointer transition-all">
                                <input type="checkbox" name="recipient_emails[]" value="{{ $email->email }}" checked
                                    class="rounded border-surface-600 bg-surface-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0" />
                                <div>
                                    <span class="text-sm text-surface-200">{{ $email->email }}</span>
                                    <span class="text-xs text-surface-500 capitalize ml-2">{{ $email->label }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-danger-400">No email addresses found for this contact. <a
                            href="{{ route('contacts.edit', $contact) }}" class="underline">Add one</a>.</p>
                @endif
                @error('recipient_emails')
                    <p class="mt-2 text-sm text-danger-400">{{ $message }}</p>
                @enderror

                <div class="mt-5 pt-5 border-t border-surface-700/50">
                    <label for="cc_emails" class="block text-sm font-medium text-surface-200 mb-2">CC (Optional)</label>
                    <select name="cc_emails[]" id="cc_emails" multiple placeholder="Add CC email..."
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-3 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                        @foreach($existingCCs as $cc)
                            <option value="{{ $cc }}" {{ (is_array(old('cc_emails')) && in_array($cc, old('cc_emails'))) ? 'selected' : '' }}>{{ $cc }}</option>
                        @endforeach
                    </select>
                    @error('cc_emails')
                        <p class="mt-2 text-sm text-danger-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Step 2: Select Configuration -->
            <div class="glass rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-1">Step 2 — Select Email Configuration</h3>
                <p class="text-sm text-surface-400 mb-5">Choose the sender</p>
                @if ($configurations->count() > 0)
                    <select name="email_configuration_id" id="config-select" required
                        onchange="loadTemplatePreview(document.getElementById('template-select').value, {{ $contact->id }}, this.value)"
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-3 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                        <option value="">Select configuration...</option>
                        @foreach ($configurations as $config)
                            <option value="{{ $config->id }}">{{ $config->from_name }} &lt;{{ $config->from_email }}&gt;
                                ({{ $config->name }})
                            </option>
                        @endforeach
                    </select>
                @else
                    <p class="text-sm text-danger-400">No active email configurations. Ask an admin to configure one.</p>
                @endif
                @error('email_configuration_id')
                    <p class="mt-2 text-sm text-danger-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Step 3: Select Template -->
            <div class="glass rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-1">Step 3 — Select Template</h3>
                <p class="text-sm text-surface-400 mb-5">Choose an email template</p>
                @if ($templates->count() > 0)
                    <select name="email_template_id" id="template-select" required
                        onchange="loadTemplatePreview(this.value, {{ $contact->id }}, document.getElementById('config-select').value)"
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-3 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                        <option value="">Select template...</option>
                        @foreach ($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                    <!-- Preview -->
                    <div id="template-preview" class="mt-4 p-4 rounded-xl bg-surface-800 border border-surface-700 hidden">
                        <p class="text-xs font-semibold uppercase tracking-wider text-surface-400 mb-2">Preview</p>
                        <p class="text-sm font-medium text-surface-200 mb-2">Subject: <span id="preview-subject"
                                class="text-primary-400"></span></p>
                        <iframe id="preview-body" class="w-full mt-2 bg-transparent rounded-lg border border-surface-700"
                            style="height: 500px;" srcdoc=""></iframe>
                    </div>
                @else
                    <p class="text-sm text-danger-400">No active templates. <a href="{{ route('email-templates.create') }}"
                            class="underline">Create one</a>.</p>
                @endif
                @error('email_template_id')
                    <p class="mt-2 text-sm text-danger-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Step 4: Attachments -->
            <div class="glass rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-1">Step 4 — Attachments</h3>
                <p class="text-sm text-surface-400 mb-5">Toggle card image attachments</p>
                <div class="space-y-3">
                    @foreach (['front' => 'Card Front Image', 'back' => 'Card Back Image', 'other' => 'Other Image'] as $type => $label)
                        @php $img = $contact->images->where('type', $type)->first(); @endphp
                        <label
                            class="flex items-center gap-4 p-3 rounded-xl bg-surface-800/50 border border-surface-700/50 {{ $img ? 'hover:border-primary-500/30 cursor-pointer' : 'opacity-50 cursor-not-allowed' }} transition-all">
                            <input type="checkbox" name="attach_{{ $type }}" value="1"
                                {{ $img ? 'checked' : 'disabled' }}
                                class="rounded border-surface-600 bg-surface-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0" />
                            @if ($img)
                                <img src="{{ Storage::url($img->file_path) }}" class="w-12 h-12 rounded-lg object-cover" />
                            @else
                                <div
                                    class="w-12 h-12 rounded-lg bg-surface-800 border border-surface-700 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-surface-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14" />
                                    </svg>
                                </div>
                            @endif
                            <span class="text-sm text-surface-300">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Step 5: Send -->
            <div class="flex items-center justify-between">
                <a href="{{ route('contacts.show', $contact) }}"
                    class="px-5 py-2.5 rounded-xl text-sm font-medium text-surface-400 hover:text-surface-200 transition-colors">←
                    Back to Contact</a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-accent-500 to-accent-600 text-white text-sm font-semibold hover:from-accent-400 hover:to-accent-500 transition-all shadow-lg shadow-accent-500/20 hover:shadow-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Send Now
                </button>
            </div>
        </form>
    </div>

@push('scripts')
    <script>
        // Initialize Tom Select for CC emails
        new TomSelect('#cc_emails', {
            persist: false,
            createOnBlur: true,
            create: true,
            plugins: ['remove_button'],
            dropdownParent: 'body',
            onItemAdd: function() {
                this.setTextboxValue('');
                this.refreshOptions();
            }
        });

        // Show preview panel when data loads
        var originalLoadPreview = window.loadTemplatePreview;
        window.loadTemplatePreview = function(templateId, contactId, configId) {
            if (templateId && contactId) {
                document.getElementById('template-preview').classList.remove('hidden');
            }
            originalLoadPreview(templateId, contactId, configId);
        };
    </script>
@endpush
@endsection
