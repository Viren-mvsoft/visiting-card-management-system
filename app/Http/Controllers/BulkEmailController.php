<?php

namespace App\Http\Controllers;

use App\Jobs\SendContactEmailJob;
use App\Models\Contact;
use App\Models\EmailConfiguration;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class BulkEmailController extends Controller
{
    public function create(Request $request)
    {
        $ids = explode(',', $request->query('contact_ids', ''));
        $contacts = Contact::whereIn('id', $ids)->with(['emails', 'images'])->get();

        if ($contacts->isEmpty()) {
            return redirect()->route('contacts.index')->with('error', 'No contacts selected.');
        }

        $user = $request->user();
        // Authorize: ensure user can access these contacts
        foreach ($contacts as $contact) {
            if (! $user->isAdmin() && $contact->user_id !== $user->id) {
                abort(403);
            }
        }

        $configurations = EmailConfiguration::active()->get();
        $templates = EmailTemplate::active()->get();

        return view('emails.bulk', compact('contacts', 'configurations', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contact_ids' => 'required|string',
            'email_configuration_id' => 'required|exists:email_configurations,id',
            'email_template_id' => 'required|exists:email_templates,id',
            'attach_front' => 'nullable|boolean',
            'attach_back' => 'nullable|boolean',
            'attach_other' => 'nullable|boolean',
        ]);

        $ids = explode(',', $validated['contact_ids']);
        $contacts = Contact::whereIn('id', $ids)->with(['emails', 'images'])->get();
        $config = EmailConfiguration::findOrFail($validated['email_configuration_id']);
        $template = EmailTemplate::findOrFail($validated['email_template_id']);
        $user = $request->user();

        $queuedCount = 0;

        foreach ($contacts as $contact) {
            // Authorize
            if (! $user->isAdmin() && $contact->user_id !== $user->id) {
                continue;
            }

            // Skip if unsubscribed
            if ($contact->isUnsubscribed()) {
                continue;
            }

            $recipientEmails = $contact->emails->pluck('email')->toArray();
            if (empty($recipientEmails)) {
                continue;
            }

            // Render template for this contact
            $renderedSubject = $template->renderSubject($contact, $config->from_name);
            $renderedBody = $template->renderBody($contact, $config->from_name);

            // Determine attachments for this contact
            $attachments = [];
            if ($request->boolean('attach_front')) {
                $img = $contact->images->where('type', 'front')->first();
                if ($img) $attachments[] = ['path' => $img->file_path, 'name' => $img->file_name];
            }
            if ($request->boolean('attach_back')) {
                $img = $contact->images->where('type', 'back')->first();
                if ($img) $attachments[] = ['path' => $img->file_path, 'name' => $img->file_name];
            }
            if ($request->boolean('attach_other')) {
                $img = $contact->images->where('type', 'other')->first();
                if ($img) $attachments[] = ['path' => $img->file_path, 'name' => $img->file_name];
            }

            // Create email log
            $emailLog = EmailLog::create([
                'contact_id' => $contact->id,
                'user_id' => $user->id,
                'email_configuration_id' => $config->id,
                'email_template_id' => $template->id,
                'recipients' => $recipientEmails,
                'cc' => [], // Bulk sending typically doesn't use CC for individual personalization
                'subject' => $renderedSubject,
                'body' => $renderedBody,
                'attachments' => $attachments,
                'status' => 'pending',
            ]);

            // Dispatch job (using the real queue, not sync)
            SendContactEmailJob::dispatch($emailLog);
            $queuedCount++;
        }

        return redirect()->route('contacts.index')
            ->with('success', "$queuedCount emails have been queued for sending.");
    }
}
