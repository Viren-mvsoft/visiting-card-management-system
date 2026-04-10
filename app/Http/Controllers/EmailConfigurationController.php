<?php

namespace App\Http\Controllers;

use App\Models\EmailConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailConfigurationController extends Controller
{
    public function index()
    {
        $configurations = EmailConfiguration::latest()->paginate(25);
        return view('email-configs.index', compact('configurations'));
    }

    public function create()
    {
        return view('email-configs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'encryption' => 'required|string|in:tls,ssl,none',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:500',
            'status' => 'required|string|in:active,inactive',
        ]);

        EmailConfiguration::create($validated);

        return redirect()->route('email-configs.index')
            ->with('success', 'Email configuration created successfully.');
    }

    public function edit(EmailConfiguration $emailConfig)
    {
        return view('email-configs.edit', compact('emailConfig'));
    }

    public function update(Request $request, EmailConfiguration $emailConfig)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'encryption' => 'required|string|in:tls,ssl,none',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:500',
            'status' => 'required|string|in:active,inactive',
        ]);

        // Only update password if provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $emailConfig->update($validated);

        return redirect()->route('email-configs.index')
            ->with('success', 'Email configuration updated successfully.');
    }

    public function destroy(EmailConfiguration $emailConfig)
    {
        $emailConfig->delete();

        return redirect()->route('email-configs.index')
            ->with('success', 'Email configuration deleted successfully.');
    }

    public function test(Request $request, EmailConfiguration $emailConfig)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            // Determine TLS mode: true = implicit TLS (SSL), null = STARTTLS (TLS), false = no encryption
            $tls = match (true) {
                $emailConfig->port == 465 => true,
                $emailConfig->port == 587 => null,
                $emailConfig->encryption === 'ssl' => true,
                $emailConfig->encryption === 'none' => false,
                default => null, // 'tls' -> STARTTLS
            };

            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                $emailConfig->host,
                $emailConfig->port,
                $tls
            );

            if ($emailConfig->username) {
                $transport->setUsername($emailConfig->username);
                $transport->setPassword($emailConfig->password);
            }

            $mailer = new \Symfony\Component\Mailer\Mailer($transport);

            $email = (new \Symfony\Component\Mime\Email())
                ->from(new \Symfony\Component\Mime\Address($emailConfig->from_email, $emailConfig->from_name))
                ->to($request->test_email)
                ->subject('VCMS - Test Email')
                ->html('<h2>Test Email</h2><p>This is a test email from your VCMS email configuration: <strong>' . $emailConfig->name . '</strong>.</p><p>If you received this, your SMTP settings are working correctly!</p>');

            $mailer->send($email);

            return back()->with('success', 'Test email sent successfully to ' . $request->test_email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}
