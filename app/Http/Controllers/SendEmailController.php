<?php

namespace App\Http\Controllers;

use App\Jobs\SendContactEmailJob;
use App\Models\Contact;
use App\Models\EmailConfiguration;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Setting;
use Illuminate\Http\Request;

class SendEmailController extends Controller
{
    public function create(Request $request, Contact $contact)
    {
        // Authorize access
        $user = $request->user();
        if (! $user->isAdmin() && $contact->user_id !== $user->id) {
            abort(403);
        }

        $contact->load(['emails', 'images']);
        $configurations = EmailConfiguration::active()->get();
        $templates = EmailTemplate::active()->get();

        return view('emails.send', compact('contact', 'configurations', 'templates'));
    }

    public function store(Request $request, Contact $contact)
    {
        $user = $request->user();
        if (! $user->isAdmin() && $contact->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'recipient_emails' => 'required|array|min:1',
            'recipient_emails.*' => 'required|email',
            'cc_emails' => 'nullable|string',
            'email_configuration_id' => 'required|exists:email_configurations,id',
            'email_template_id' => 'required|exists:email_templates,id',
            'attach_front' => 'nullable|boolean',
            'attach_back' => 'nullable|boolean',
            'attach_other' => 'nullable|boolean',
        ]);

        $config = EmailConfiguration::findOrFail($validated['email_configuration_id']);
        $template = EmailTemplate::findOrFail($validated['email_template_id']);

        // Render template with contact variables
        $renderedSubject = $template->renderSubject($contact, $config->from_name);
        $renderedBody = $template->renderBody($contact, $config->from_name);

        // Determine attachments
        $attachments = [];
        $contact->load('images');

        if ($request->boolean('attach_front')) {
            $img = $contact->images->where('type', 'front')->first();
            if ($img) {
                $attachments[] = ['path' => $img->file_path, 'name' => $img->file_name];
            }
        }
        if ($request->boolean('attach_back')) {
            $img = $contact->images->where('type', 'back')->first();
            if ($img) {
                $attachments[] = ['path' => $img->file_path, 'name' => $img->file_name];
            }
        }
        if ($request->boolean('attach_other')) {
            $img = $contact->images->where('type', 'other')->first();
            if ($img) {
                $attachments[] = ['path' => $img->file_path, 'name' => $img->file_name];
            }
        }

        $ccEmails = [];
        if (! empty($validated['cc_emails'])) {
            $ccList = array_map('trim', explode(',', $validated['cc_emails']));
            foreach ($ccList as $cc) {
                if (filter_var($cc, FILTER_VALIDATE_EMAIL)) {
                    $ccEmails[] = $cc;
                }
            }
        }

        // Create email log
        $emailLog = EmailLog::create([
            'contact_id' => $contact->id,
            'user_id' => $user->id,
            'email_configuration_id' => $config->id,
            'email_template_id' => $template->id,
            'recipients' => $validated['recipient_emails'],
            'cc' => $ccEmails,
            'subject' => $renderedSubject,
            'body' => $renderedBody,
            'attachments' => $attachments,
            'status' => 'pending',
        ]);

        // Send immediately (dispatchSync bypasses the queue worker requirement)
        SendContactEmailJob::dispatchSync($emailLog);

        return redirect()->route('contacts.show', $contact)
            ->with('success', 'Email has been queued for sending.');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id',
            'contact_id' => 'required|exists:contacts,id',
            'config_id' => 'nullable|exists:email_configurations,id',
        ]);

        $template = EmailTemplate::findOrFail($request->template_id);
        $contact = Contact::findOrFail($request->contact_id);
        $senderName = null;

        if ($request->config_id) {
            $config = EmailConfiguration::findOrFail($request->config_id);
            $senderName = $config->from_name;
        }

        // Fetch global settings
        $settings = Setting::pluck('value', 'key')->toArray();
        $isThemeEnabled = ($settings['email_theme_enabled'] ?? '0') === '1';
        $renderedBodyContent = $template->renderBody($contact, $senderName);

        if ($isThemeEnabled) {
            $themeName = $settings['email_theme'] ?? 'default';
            $themeView = 'emails.themes.'.$themeName;

            if (! view()->exists($themeView)) {
                $themeView = 'emails.themes.default';
            }

            $compiledHtml = view($themeView, [
                'body' => $renderedBodyContent,
                'settings' => $settings,
            ])->render();
        } else {
            $compiledHtml = $renderedBodyContent;
        }

        return response()->json([
            'subject' => $template->renderSubject($contact, $senderName),
            'body' => $compiledHtml,
        ]);
    }
}
