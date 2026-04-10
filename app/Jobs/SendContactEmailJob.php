<?php

namespace App\Jobs;

use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SendContactEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public EmailLog $emailLog
    ) {}

    public function handle(): void
    {
        $log = $this->emailLog;
        $config = $log->configuration;

        if (!$config) {
            $log->update(['status' => 'failed', 'error' => 'Email configuration not found.']);
            return;
        }

        try {
            // Determine TLS mode: true = implicit TLS (SSL), null = STARTTLS (TLS), false = no encryption
            $tls = match (true) {
                $config->port == 465 => true,
                $config->port == 587 => null,
                $config->encryption === 'ssl' => true,
                $config->encryption === 'none' => false,
                default => null, // 'tls' -> STARTTLS
            };

            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                $config->host,
                $config->port,
                $tls
            );

            if ($config->username) {
                $transport->setUsername($config->username);
                $transport->setPassword($config->password);
            }

            $mailer = new \Symfony\Component\Mailer\Mailer($transport);

            // Fetch global settings
            $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
            
            // Determine theme view
            $themeName = $settings['email_theme'] ?? 'default';
            $themeView = 'emails.themes.' . $themeName;
            
            // Fallback to default if somehow not exists
            if (!view()->exists($themeView)) {
                $themeView = 'emails.themes.default';
            }

            // Render HTML body using the theme
            $compiledHtml = view($themeView, [
                'body' => $log->body,
                'settings' => $settings
            ])->render();

            $email = (new Email())
                ->from(new Address($config->from_email, $config->from_name))
                ->subject($log->subject)
                ->html($compiledHtml);

            // Add recipients
            foreach ($log->recipients as $recipient) {
                $email->addTo($recipient);
            }

            // Add CC
            if (!empty($log->cc)) {
                foreach ($log->cc as $ccAddress) {
                    $email->addCc($ccAddress);
                }
            }

            // Add attachments
            if (!empty($log->attachments)) {
                foreach ($log->attachments as $attachment) {
                    $filePath = Storage::disk('public')->path($attachment['path']);
                    if (file_exists($filePath)) {
                        $email->attachFromPath($filePath, $attachment['name']);
                    }
                }
            }

            $mailer->send($email);

            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error' => null,
            ]);
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
