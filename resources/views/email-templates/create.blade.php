@extends('layouts.app')
@section('title', 'Create Template')
@section('subtitle', 'Build a new email template')

@section('content')
<div class="max-w-3xl mx-auto animate-fade-in">
    <form method="POST" action="{{ route('email-templates.store') }}">
        @csrf
        <div class="glass rounded-2xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-white mb-5">Template Details</h3>
            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Template Name <span class="text-danger-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Follow-up Email"
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                        @error('name') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Status</label>
                        <select name="status" class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Subject Line <span class="text-danger-400">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="Great meeting you at @{{event}}, @{{name}}!"
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                    @error('subject') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Body (supports HTML) <span class="text-danger-400">*</span></label>
                    <!-- Variable insertion buttons -->
                    <div class="flex flex-wrap gap-2 mb-2">
                        @php $templateVars = ['@{{name}}', '@{{company}}', '@{{event}}', '@{{country}}', '@{{sender_name}}']; @endphp
                        @foreach($templateVars as $var)
                            <button type="button"
                                onclick="insertVariable('{{ str_replace('@', '', $var) }}')"
                                class="px-2.5 py-1 rounded-lg text-xs font-medium bg-surface-800 text-primary-400 hover:bg-primary-500/10 border border-surface-700 hover:border-primary-500/30 transition-all">
                                {{ str_replace('@', '', $var) }}
                            </button>
                        @endforeach
                    </div>
                    <textarea id="template-body" name="body" rows="12" required
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-3 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all resize-y font-mono"
                        placeholder="Dear @{{name}},&#10;&#10;It was great meeting you at @{{event}}...">{{ old('body') }}</textarea>
                    @error('body') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('email-templates.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-surface-400 hover:text-surface-200 transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20">
                Create Template
            </button>
        </div>
    </form>
</div>

<script>
function insertVariable(variable) {
    var textarea = document.getElementById('template-body');
    var start = textarea.selectionStart;
    var end = textarea.selectionEnd;
    var text = textarea.value;
    textarea.value = text.substring(0, start) + variable + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + variable.length;
    textarea.focus();
}
</script>
@endsection
