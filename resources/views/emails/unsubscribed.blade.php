<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribed — Visiting Card Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #0f172a; color: #f8fafc; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="h-full flex items-center justify-center p-6">
    <div class="max-w-md w-full glass rounded-3xl p-8 text-center animate-fade-in">
        <div class="w-20 h-20 bg-primary-500/10 border border-primary-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-white mb-2">Unsubscribed Successfully</h1>
        <p class="text-slate-400 mb-8">
            The email address associated with <strong>{{ $contact->name }}</strong> has been removed from our bulk mailing list. You will no longer receive automated marketing or announcement emails from us.
        </p>

        <div class="text-xs text-slate-500 italic">
            If this was a mistake, please contact the person who shared their card with you to be re-added.
        </div>
    </div>
</body>
</html>
