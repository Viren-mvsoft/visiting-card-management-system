<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->isAdmin()
            ? EmailLog::query()
            : $user->emailLogs();

        $logs = $query->with(['contact', 'user', 'configuration', 'template'])
            ->latest('created_at')
            ->paginate(25);

        return view('email-logs.index', compact('logs'));
    }
}
