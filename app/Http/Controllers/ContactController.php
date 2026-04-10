<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactEmail;
use App\Models\ContactImage;
use App\Models\ContactPhone;
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
                  ->orWhere('event', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($country = $request->input('country')) {
            $query->where('country', $country);
        }
        if ($company = $request->input('company')) {
            $query->where('company_name', 'like', "%{$company}%");
        }
        if ($event = $request->input('event')) {
            $query->where('event', $event);
        }
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $perPage = in_array($request->input('per_page'), ['25', '50', '100', '500']) ? (int) $request->input('per_page') : 25;

        $contacts = $query->with(['user', 'phones', 'emails', 'images'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        // Get filter options
        $baseQuery = $user->isAdmin() ? Contact::query() : $user->contacts();
        $countries = $baseQuery->distinct()->whereNotNull('country')->pluck('country')->sort();
        $events = ($user->isAdmin() ? Contact::query() : $user->contacts())
            ->distinct()->whereNotNull('event')->pluck('event')->sort();

        return view('contacts.index', compact('contacts', 'countries', 'events'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'event' => 'nullable|string|max:255',
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

        $contact = $request->user()->contacts()->create([
            'name' => $validated['name'],
            'country' => $validated['country'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'event' => $validated['event'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Save phones
        if (!empty($validated['phones'])) {
            foreach ($validated['phones'] as $phone) {
                if (!empty($phone['phone'])) {
                    $contact->phones()->create($phone);
                }
            }
        }

        // Save emails
        if (!empty($validated['emails'])) {
            foreach ($validated['emails'] as $email) {
                if (!empty($email['email'])) {
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

        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorizeAccess($request, $contact);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'event' => 'nullable|string|max:255',
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

        $contact->update([
            'name' => $validated['name'],
            'country' => $validated['country'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'event' => $validated['event'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Sync phones
        $contact->phones()->delete();
        if (!empty($validated['phones'])) {
            foreach ($validated['phones'] as $phone) {
                if (!empty($phone['phone'])) {
                    $contact->phones()->create($phone);
                }
            }
        }

        // Sync emails
        $contact->emails()->delete();
        if (!empty($validated['emails'])) {
            foreach ($validated['emails'] as $email) {
                if (!empty($email['email'])) {
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
        if (!$user->isAdmin() && $contact->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this contact.');
        }
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
}
