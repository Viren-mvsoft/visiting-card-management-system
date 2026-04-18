<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class UnsubscribeController extends Controller
{
    public function unsubscribe(Request $request, Contact $contact)
    {
        // Mark as unsubscribed if not already
        if (!$contact->isUnsubscribed()) {
            $contact->update([
                'unsubscribed_at' => now()
            ]);
        }

        return view('emails.unsubscribed', compact('contact'));
    }
}
