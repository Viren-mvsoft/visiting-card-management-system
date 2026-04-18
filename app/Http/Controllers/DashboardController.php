<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\EmailLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $totalContacts = Contact::count();
            $emailsSent = EmailLog::where('status', 'sent')
                ->where('sent_at', '>=', now()->subDays(30))
                ->count();
            $recentContacts = Contact::with('user')
                ->latest()
                ->take(5)
                ->get();
            $topEvents = Contact::whereNotNull('event_id')
                ->selectRaw('event_id, COUNT(*) as count')
                ->groupBy('event_id')
                ->orderByDesc('count')
                ->with('event')
                ->take(5)
                ->get();
            $recentEmails = EmailLog::with(['contact', 'user', 'configuration', 'template'])
                ->latest('sent_at')
                ->take(5)
                ->get();
        } else {
            $totalContacts = $user->contacts()->count();
            $emailsSent = $user->emailLogs()
                ->where('status', 'sent')
                ->where('sent_at', '>=', now()->subDays(30))
                ->count();
            $recentContacts = $user->contacts()
                ->latest()
                ->take(5)
                ->get();
            $topEvents = $user->contacts()
                ->whereNotNull('event_id')
                ->selectRaw('event_id, COUNT(*) as count')
                ->groupBy('event_id')
                ->orderByDesc('count')
                ->with('event')
                ->take(5)
                ->get();
            $recentEmails = $user->emailLogs()
                ->with(['contact', 'configuration', 'template'])
                ->latest('sent_at')
                ->take(5)
                ->get();
        }

        return view('dashboard', compact(
            'totalContacts',
            'emailsSent',
            'recentContacts',
            'topEvents',
            'recentEmails'
        ));
    }
}
