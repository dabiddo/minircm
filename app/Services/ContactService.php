<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactService
{
    public function searchAndFilter(Request $request)
    {
        $contacts = Contact::query();

        // only email will have exact match search
        if ($request->filled('email')) {
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

        return $contacts->get();
    }
}
