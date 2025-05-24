<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ContactService $contactService)
    {
        $contacts = $contactService->searchAndFilter($request);

        return response()->json(['data' => $contacts], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        $validatedData = $request->validated();
        $contact = Contact::create($validatedData);

        return response()->json(['data' => $contact], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company, Contact $contact)
    {
        return response()->json(['data' => $contact], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $contact->update($request->validated());

        return response()->json($contact, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, Contact $contact)
    {

        $contact->delete();

        return response()->json(null, 204);
    }

    public function deals(Request $request, Contact $contact)
    {
        $deals = $contact->deals;

        return response()->json(['data' => $deals]);
    }
}
