<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $companyName = $settings['company_name'] ?? config('app.name', 'VCMS');
        $companyLogo = $settings['company_logo'] ?? null;
    @endphp

    <title>{{ $companyName }} — Login</title>

    @if ($companyLogo)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($companyLogo) }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-surface-950 text-surface-200">
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <!-- Logo -->
        <div class="mb-8 text-center animate-fade-in">
            @if ($companyLogo)
                <img src="{{ Storage::url($companyLogo) }}" alt="{{ $companyName }}"
                    class="h-16 w-auto mx-auto object-contain mb-4">
            @else
                <div
                    class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-xl shadow-primary-500/20 mb-4">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
            @endif
            <h1 class="text-2xl font-bold text-white">{{ $companyName }}</h1>
            <p class="text-sm text-surface-400 mt-1">Visiting Card Management System</p>
        </div>

        <!-- Card -->
        <div class="w-full max-w-md">
            <div class="glass rounded-2xl p-8 shadow-2xl animate-scale-in">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>
