@extends('layouts.app')
@section('title', 'Global Settings')
@section('subtitle', 'Configure system-wide settings and email layout')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in mb-10">
    <form method="POST" action="{{ route('settings.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Branding -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Branding Card -->
                <div class="glass rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-5">Branding</h3>
                    
                    <div class="space-y-5">
                        <!-- Company Name -->
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Company Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? '') }}" placeholder="e.g. VCMS Corp"
                                class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 xl:transition-all" />
                        </div>
                        
                        <!-- Company Logo -->
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Company Logo</label>
                            
                            <div id="logo-preview-container" class="mb-5 p-5 rounded-xl border border-surface-700 {{ !empty($settings['company_logo']) ? 'inline-block' : 'hidden' }} min-w-[200px]" style="background-image: linear-gradient(45deg, #1e293b 25%, transparent 25%), linear-gradient(-45deg, #1e293b 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #1e293b 75%), linear-gradient(-45deg, transparent 75%, #1e293b 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px; background-color: #0f172a;">
                                <img id="logo-preview-image" src="{{ !empty($settings['company_logo']) ? Storage::url($settings['company_logo']) : '#' }}" alt="Current Logo" class="max-h-24 w-auto object-contain mx-auto mix-blend-normal" />
                            </div>
                            
                            <input type="file" name="company_logo" id="company_logo_input" accept="image/*"
                                onchange="handleLogoPreview(this)"
                                class="block w-full text-sm text-surface-400
                                  file:mr-4 file:py-2.5 file:px-4
                                  file:rounded-xl file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-surface-800 file:text-primary-400
                                  hover:file:bg-surface-700 file:transition-colors file:cursor-pointer
                                  rounded-xl border border-surface-700 bg-surface-800 focus:outline-none" />
                            <p class="mt-2 text-xs text-surface-500">PNG, JPG or GIF up to 2MB. Recommended height: 50px. Selected logo should be in horizontal format.</p>
                            <p id="logo-size-error" class="mt-2 text-xs text-danger-400 hidden">File size exceeds 2MB limit!</p>
                            @error('company_logo') <p class="mt-2 text-xs text-danger-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Email Theme -->
                <div class="glass rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-5">Email Theme</h3>
                    <p class="text-sm text-surface-400 mb-5">Select the master layout that will wrap all your outgoing emails.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @php $currentTheme = old('email_theme', $settings['email_theme'] ?? 'default'); @endphp
                        
                        <!-- Default Theme -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="email_theme" value="default" class="peer sr-only" {{ $currentTheme === 'default' ? 'checked' : '' }}>
                            <div class="p-4 rounded-xl border-2 border-surface-700 bg-surface-800 peer-checked:border-primary-500 peer-checked:bg-primary-500/5 transition-all text-center">
                                <div class="w-full h-24 bg-white rounded-md mb-3 flex flex-col pt-3 px-2 border border-surface-600">
                                    <div class="w-1/2 h-2 bg-primary-100 rounded mb-2 mx-auto"></div>
                                    <div class="w-full bg-surface-100 rounded flex-1"></div>
                                </div>
                                <span class="text-sm font-medium text-surface-200 block">Clean Container</span>
                            </div>
                        </label>
                        
                        <!-- Dark Theme -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="email_theme" value="dark" class="peer sr-only" {{ $currentTheme === 'dark' ? 'checked' : '' }}>
                            <div class="p-4 rounded-xl border-2 border-surface-700 bg-surface-800 peer-checked:border-primary-500 peer-checked:bg-primary-500/5 transition-all text-center">
                                <div class="w-full h-24 bg-surface-900 rounded-md mb-3 flex flex-col pt-3 px-2 border border-surface-600 shadow-inner">
                                    <div class="w-1/3 h-2 bg-surface-700 rounded mb-2"></div>
                                    <div class="w-full bg-surface-800 rounded flex-1"></div>
                                </div>
                                <span class="text-sm font-medium text-surface-200 block">Minimalist Dark</span>
                            </div>
                        </label>
                        
                        <!-- Bold Theme -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="email_theme" value="bold" class="peer sr-only" {{ $currentTheme === 'bold' ? 'checked' : '' }}>
                            <div class="p-4 rounded-xl border-2 border-surface-700 bg-surface-800 peer-checked:border-primary-500 peer-checked:bg-primary-500/5 transition-all text-center">
                                <div class="w-full h-24 bg-white rounded-md mb-3 flex flex-col overflow-hidden border border-surface-600">
                                    <div class="w-full h-6 bg-gradient-to-r from-red-400 to-red-600"></div>
                                    <div class="w-full bg-white rounded mt-1 flex-1 px-2"></div>
                                </div>
                                <span class="text-sm font-medium text-surface-200 block">Bold Corporate</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Links -->
            <div class="space-y-6">
                <div class="glass rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-5">Website & Social Media</h3>
                    <p class="text-sm text-surface-400 mb-5">These links will appear in the footer of your email template.</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Website Link</label>
                            <input type="url" name="website_link" value="{{ old('website_link', $settings['website_link'] ?? '') }}" placeholder="https://..."
                                class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Facebook</label>
                            <input type="url" name="facebook_link" value="{{ old('facebook_link', $settings['facebook_link'] ?? '') }}" placeholder="https://facebook.com/..."
                                class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Twitter / X</label>
                            <input type="url" name="twitter_link" value="{{ old('twitter_link', $settings['twitter_link'] ?? '') }}" placeholder="https://twitter.com/..."
                                class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">LinkedIn</label>
                            <input type="url" name="linkedin_link" value="{{ old('linkedin_link', $settings['linkedin_link'] ?? '') }}" placeholder="https://linkedin.com/..."
                                class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Instagram</label>
                            <input type="url" name="instagram_link" value="{{ old('instagram_link', $settings['instagram_link'] ?? '') }}" placeholder="https://instagram.com/..."
                                class="w-full rounded-xl border border-surface-700 bg-surface-800 px-4 py-2.5 text-sm text-surface-200 placeholder-surface-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500" />
                        </div>
                    </div>
                </div>
                
                <!-- Action button -->
                <div class="glass rounded-2xl p-6 flex flex-col items-center">
                    <button type="submit" class="w-full px-6 py-3 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-bold hover:from-primary-400 hover:to-primary-500 transition-all shadow-lg shadow-primary-500/20">
                        Save Settings
                    </button>
                    @if(session('success'))
                        <div class="mt-4 px-4 py-2 bg-success-500/10 border border-success-500/20 rounded-lg text-success-400 text-sm w-full text-center animate-fade-in">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
<script>
function handleLogoPreview(input) {
    const container = document.getElementById('logo-preview-container');
    const image = document.getElementById('logo-preview-image');
    const errorMsg = document.getElementById('logo-size-error');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            errorMsg.classList.remove('hidden');
            input.value = ''; // Reset input
            return;
        } else {
            errorMsg.classList.add('hidden');
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            image.src = e.target.result;
            container.classList.remove('hidden');
            container.classList.add('inline-block');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
