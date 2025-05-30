<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContactService
{
    public function searchAndFilter(Request $request)
    {
        $contacts = Contact::query();

        // only email will have exact match search
        if ($request->filled('email')) {
            $validated = $request->validate([
                'email' => 'email',
            ]);
            if (! $validated) {
                throw ValidationException::withMessages(['email' => 'Not a Valid Email']);
            }
            $contacts->where('email', $request->input('email'));
        }
        if ($request->filled('first_name')) {
            $contacts->where('first_name', 'like', '%'.$request->first_name.'%');
        }

        if ($request->filled('last_name')) {
            $contacts->where('last_name', 'like', '%'.$request->last_name.'%');
        }

        if ($request->filled('phone_number')) {
            $contacts->where('phone_number', 'like', '%'.$request->phone_number.'%');
        }

        if ($request->has('withTrashed') && $request->input('withTrashed') == 'true') {
            $contacts->withTrashed();
        }

        return $contacts->get();
    }
}
