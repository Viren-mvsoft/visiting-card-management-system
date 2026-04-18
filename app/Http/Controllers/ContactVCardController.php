<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContactVCardController extends Controller
{
    /**
     * Download vCard for an existing contact.
     */
    public function download(Contact $contact)
    {
        return $this->generateVCard(
            $contact->name,
            $contact->company_name,
            $contact->emails()->pluck('email')->toArray(),
            $contact->phones()->pluck('phone')->toArray(),
            $contact->address,
            $contact->website
        );
    }

    /**
     * Preview/Download vCard from unsaved form data.
     */
    public function preview(Request $request)
    {
        // Extract emails and phones from the request array structure
        $emails = collect($request->input('emails', []))->pluck('email')->filter()->toArray();
        $phones = collect($request->input('phones', []))->pluck('phone')->filter()->toArray();

        return $this->generateVCard(
            $request->input('name', 'Contact'),
            $request->input('company_name'),
            $emails,
            $phones,
            $request->input('address'),
            $request->input('website')
        );
    }

    /**
     * Core VCF generation logic.
     */
    protected function generateVCard($name, $company, $emails, $phones, $address, $website)
    {
        $vCard = "BEGIN:VCARD\n";
        $vCard .= "VERSION:3.0\n";
        $vCard .= "FN:" . $this->escapeVCard($name) . "\n";
        
        if ($company) {
            $vCard .= "ORG:" . $this->escapeVCard($company) . "\n";
        }

        foreach ($emails as $email) {
            $vCard .= "EMAIL;TYPE=INTERNET:" . $this->escapeVCard($email) . "\n";
        }

        foreach ($phones as $phone) {
            $vCard .= "TEL;TYPE=CELL:" . $this->escapeVCard($phone) . "\n";
        }

        if ($address) {
            $vCard .= "ADR;TYPE=WORK:;;" . $this->escapeVCard($address) . ";;;;\n";
        }

        if ($website) {
            $vCard .= "URL:" . $this->escapeVCard($website) . "\n";
        }

        $vCard .= "END:VCARD";

        $filename = Str::slug($name ?: 'contact') . '.vcf';

        return response($vCard)
            ->header('Content-Type', 'text/vcard')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    protected function escapeVCard($string)
    {
        return str_replace([",", ";", "\n"], ["\\,", "\\;", "\\n"], $string);
    }
}
