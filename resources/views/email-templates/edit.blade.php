@extends('layouts.app')
@section('title', 'Edit Template')
@section('subtitle', $emailTemplate->name)

@section('content')
<div class="max-w-3xl mx-auto animate-fade-in">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow { border-color: #334155 !important; background: #1e293b; border-radius: 0.75rem 0.75rem 0 0; border: 1px solid #334155; }
        .ql-container.ql-snow { border-color: #334155 !important; border-radius: 0 0 0.75rem 0.75rem; border: 1px solid #334155; font-family: 'Inter', sans-serif; font-size: 0.875rem; color: #e2e8f0; }
        .ql-snow .ql-stroke { stroke: #cbd5e1; }
        .ql-snow .ql-fill, .ql-snow .ql-stroke.ql-fill { fill: #cbd5e1; }
        .ql-snow .ql-picker { color: #cbd5e1; }
        .ql-snow .ql-picker-options { background-color: #1e293b; border-color: #334155; }
        .ql-editor { min-height: 250px; }
    </style>
    <form method="POST" action="{{ route('email-templates.update', $emailTemplate) }}" id="template-form">
        @csrf @method('PUT')
        <div class="glass rounded-2xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-white mb-5">Template Details</h3>
            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Template Name <span class="text-danger-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $emailTemplate->name) }}" required
                            class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                        @error('name') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Status</label>
                        <select name="status" class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all">
                            <option value="active" {{ old('status', $emailTemplate->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="draft" {{ old('status', $emailTemplate->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Subject Line <span class="text-danger-400">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject', $emailTemplate->subject) }}" required
                        class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-all" />
                    @error('subject') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Body <span class="text-danger-400">*</span></label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        @php $templateVars = ['@{{name}}', '@{{company}}', '@{{event}}', '@{{country}}', '@{{sender_name}}']; @endphp
                        @foreach($templateVars as $var)
                            <button type="button" onclick="insertVariable('{{ str_replace('@', '', $var) }}')"
                                class="px-2.5 py-1 rounded-lg text-xs font-medium bg-surface-800 text-primary-400 hover:bg-primary-500/10 border border-surface-700 hover:border-primary-500/30 transition-all">{{ str_replace('@', '', $var) }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="body" id="body-hidden" value="{{ old('body', $emailTemplate->body) }}">
                    <div id="editor-container" class="bg-surface-800 text-surface-200">
                        {!! old('body', $emailTemplate->body) !!}
                    </div>
                    @error('body') <p class="mt-1 text-sm text-danger-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('email-templates.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-surface-400 hover:text-surface-200 transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-semibold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20">
                Update Template
            </button>
        </div>
    </form>
</div>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
var quill = new Quill('#editor-container', {
    theme: 'snow',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ 'header': 1 }, { 'header': 2 }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'size': ['small', false, 'large', 'huge'] }],
            [{ 'color': [] }, { 'background': [] }],
            ['link', 'image'],
            ['clean']
        ]
    }
});

document.getElementById('template-form').addEventListener('submit', function() {
    var html = quill.root.innerHTML;
    // Don't send empty paragraph if that's all it is
    if (html === '<p><br></p>') html = '';
    document.getElementById('body-hidden').value = html;
});

function insertVariable(variable) {
    var range = quill.getSelection(true);
    quill.insertText(range.index, variable);
}
</script>
@endsection
