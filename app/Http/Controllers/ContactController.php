<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactImage;
use App\Models\Country;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->isAdmin() ? Contact::query() : $user->contacts();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhereHas('event', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filters
        if ($countryId = $request->input('country_id')) {
            $query->where('country_id', $countryId);
        }
        if ($company = $request->input('company')) {
            $query->where('company_name', 'like', "%{$company}%");
        }
        if ($eventId = $request->input('event_id')) {
            $query->where('event_id', $eventId);
        }
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $perPage = in_array($request->input('per_page'), ['25', '50', '100', '500']) ? (int) $request->input('per_page') : 25;

        $contacts = $query->with(['user', 'phones', 'emails', 'images', 'event'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        // Get filter options
        $baseQuery = $user->isAdmin() ? Contact::query() : $user->contacts();
        $countries = Country::orderBy('name')->get();
        $events = Event::orderBy('name')->get();

        return view('contacts.index', compact('contacts', 'countries', 'events'));
    }

    public function create()
    {
        $events = Event::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();

        return view('contacts.create', compact('events', 'countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'event_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'phones' => 'nullable|array',
            'phones.*.phone' => 'required_with:phones|string|max:50',
            'phones.*.label' => 'required_with:phones|string|in:mobile,office,other',
            'emails' => 'nullable|array',
            'emails.*.email' => 'required_with:emails|email|max:255',
            'emails.*.label' => 'required_with:emails|string|in:work,personal,other',
            'card_front' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'card_back' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'card_other' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $eventId = $validated['event_id'] ?? null;
        if ($eventId && ! is_numeric($eventId)) {
            $event = Event::firstOrCreate(['name' => $eventId]);
            $eventId = $event->id;
        }

        $contact = $request->user()->contacts()->create([
            'name' => $validated['name'],
            'country_id' => $validated['country_id'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'website' => $validated['website'] ?? null,
            'address' => $validated['address'] ?? null,
            'event_id' => $eventId,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Save phones
        if (! empty($validated['phones'])) {
            foreach ($validated['phones'] as $phone) {
                if (! empty($phone['phone'])) {
                    $contact->phones()->create($phone);
                }
            }
        }

        // Save emails
        if (! empty($validated['emails'])) {
            foreach ($validated['emails'] as $email) {
                if (! empty($email['email'])) {
                    $contact->emails()->create($email);
                }
            }
        }

        // Save images
        $this->handleImageUpload($request, $contact, 'card_front', 'front');
        $this->handleImageUpload($request, $contact, 'card_back', 'back');
        $this->handleImageUpload($request, $contact, 'card_other', 'other');

        return redirect()->route('contacts.show', $contact)
            ->with('success', 'Contact created successfully.');
    }

    public function show(Request $request, Contact $contact)
    {
        $this->authorizeAccess($request, $contact);

        $contact->load(['phones', 'emails', 'images', 'user', 'emailLogs' => function ($q) {
            $q->with(['configuration', 'template', 'user'])->latest('sent_at');
        }]);

        return view('contacts.show', compact('contact'));
    }

    public function edit(Request $request, Contact $contact)
    {
        $this->authorizeAccess($request, $contact);
        $contact->load(['phones', 'emails', 'images']);
        $events = Event::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();

        return view('contacts.edit', compact('contact', 'events', 'countries'));
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorizeAccess($request, $contact);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'event_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'phones' => 'nullable|array',
            'phones.*.phone' => 'required_with:phones|string|max:50',
            'phones.*.label' => 'required_with:phones|string|in:mobile,office,other',
            'emails' => 'nullable|array',
            'emails.*.email' => 'required_with:emails|email|max:255',
            'emails.*.label' => 'required_with:emails|string|in:work,personal,other',
            'card_front' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'card_back' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'card_other' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $eventId = $validated['event_id'] ?? null;
        if ($eventId && ! is_numeric($eventId)) {
            $event = Event::firstOrCreate(['name' => $eventId]);
            $eventId = $event->id;
        }

        $contact->update([
            'name' => $validated['name'],
            'country_id' => $validated['country_id'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'website' => $validated['website'] ?? null,
            'address' => $validated['address'] ?? null,
            'event_id' => $eventId,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Sync phones
        $contact->phones()->delete();
        if (! empty($validated['phones'])) {
            foreach ($validated['phones'] as $phone) {
                if (! empty($phone['phone'])) {
                    $contact->phones()->create($phone);
                }
            }
        }

        // Sync emails
        $contact->emails()->delete();
        if (! empty($validated['emails'])) {
            foreach ($validated['emails'] as $email) {
                if (! empty($email['email'])) {
                    $contact->emails()->create($email);
                }
            }
        }

        // Update images (only if new ones uploaded)
        $this->handleImageUpload($request, $contact, 'card_front', 'front');
        $this->handleImageUpload($request, $contact, 'card_back', 'back');
        $this->handleImageUpload($request, $contact, 'card_other', 'other');

        return redirect()->route('contacts.show', $contact)
            ->with('success', 'Contact updated successfully.');
    }

    public function destroy(Request $request, Contact $contact)
    {
        $this->authorizeAccess($request, $contact);

        // Clean up images from storage
        foreach ($contact->images as $image) {
            Storage::disk('public')->delete($image->file_path);
        }

        $contact->delete();

        return redirect()->route('contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }

    protected function handleImageUpload(Request $request, Contact $contact, string $inputName, string $type): void
    {
        if ($request->hasFile($inputName)) {
            // Delete existing image of this type
            $existing = $contact->images()->where('type', $type)->first();
            if ($existing) {
                Storage::disk('public')->delete($existing->file_path);
                $existing->delete();
            }

            $file = $request->file($inputName);
            $path = $file->store("cards/{$contact->id}", 'public');

            $contact->images()->create([
                'type' => $type,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
            ]);
        }
    }

    protected function authorizeAccess(Request $request, Contact $contact): void
    {
        $user = $request->user();
        if (! $user->isAdmin() && $contact->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this contact.');
        }
    }

    /**
     * Resubscribe a contact to bulk emails.
     */
    public function resubscribe(Request $request, Contact $contact)
    {
        $this->authorizeAccess($request, $contact);

        $contact->update(['unsubscribed_at' => null]);

        return back()->with('success', 'Contact has been resubscribed successfully.');
    }

    /**
     * Delete a specific image from a contact.
     */
    public function deleteImage(Request $request, Contact $contact, ContactImage $image)
    {
        $this->authorizeAccess($request, $contact);

        if ($image->contact_id !== $contact->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->file_path);
        $image->delete();

        return back()->with('success', 'Image deleted successfully.');
    }

    public function export(Request $request)
    {
        $user = $request->user();
        $query = $user->isAdmin() ? Contact::query() : $user->contacts();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhereHas('event', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filters
        if ($countryId = $request->input('country_id')) {
            $query->where('country_id', $countryId);
        }
        if ($company = $request->input('company')) {
            $query->where('company_name', 'like', "%{$company}%");
        }
        if ($eventId = $request->input('event_id')) {
            $query->where('event_id', $eventId);
        }
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $contacts = $query->with(['phones', 'emails', 'event', 'country'])->latest()->get();

        $filename = 'contacts_export_'.date('Y-m-d_Hmi').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($contacts) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compliance
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['ID', 'Name', 'Company', 'Website', 'Address', 'Country', 'Event', 'Phones', 'Emails', 'Notes', 'Created At']);

            foreach ($contacts as $contact) {
                $phones = $contact->phones->pluck('phone')->map(function ($p) {
                    return "\t".$p;
                })->implode('; ');

                $emails = $contact->emails->pluck('email')->implode('; ');

                fputcsv($file, [
                    $contact->id,
                    $contact->name,
                    $contact->company_name ?? 'N/A',
                    $contact->website ?? 'N/A',
                    $contact->address ?? 'N/A',
                    $contact->country->name ?? 'N/A',
                    $contact->event->name ?? 'N/A',
                    $phones,
                    $emails,
                    $contact->notes ?? '',
                    $contact->created_at->toDateTimeString(),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
