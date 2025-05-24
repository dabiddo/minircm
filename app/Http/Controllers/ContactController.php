<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['data' => Contact::all()], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email',
            'phone_number' => 'nullable|string|max:15',
        ]);
        $contact = Contact::create($validatedData);

        return response()->json(['data' => $contact], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        return response()->json(['data' => $contact], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email,'.$contact->id,
            'phone_number' => 'nullable|string|max:15',
        ]);

        $contact->update($request->all());

        return response()->json($contact, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json(null, 204);
    }
}
