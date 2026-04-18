<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    protected $fillable = ['user_id', 'name', 'subject', 'body', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Replace template variables with contact data.
     */
    public function renderSubject(Contact $contact, ?string $senderName = null): string
    {
        return $this->replaceVariables($this->subject, $contact, $senderName);
    }

    public function renderBody(Contact $contact, ?string $senderName = null): string
    {
        return $this->replaceVariables($this->body, $contact, $senderName);
    }

    protected function replaceVariables(string $text, Contact $contact, ?string $senderName = null): string
    {
        $unsubscribeUrl = \Illuminate\Support\Facades\URL::signedRoute('unsubscribe', ['contact' => $contact->id]);

        $replacements = [
            '{{name}}' => $contact->name,
            '{{company}}' => $contact->company_name ?? '',
            '{{event}}' => $contact->event->name ?? '',
            '{{country}}' => $contact->country->name ?? '',
            '{{sender_name}}' => $senderName ?? '',
            '{{unsubscribe_url}}' => $unsubscribeUrl,
            '{{unsubscribe_link}}' => '<a href="'.$unsubscribeUrl.'" style="color: #64748b; text-decoration: underline;">Unsubscribe</a>',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
}
