<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostDealRequest;
use App\Http\Requests\UpdateDealRequest;
use App\Models\Contact;
use App\Models\Deal;

class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Contact $contact)
    {
        return response()->json(['data' => Deal::all()], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Contact $contact, PostDealRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['contact_id'] = $contact->id;

        $deal = Deal::create($validatedData);

        return response()->json(['data' => $deal], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact, Deal $deal)
    {
        return response()->json(['data' => $deal], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Contact $contact, UpdateDealRequest $request, Deal $deal)
    {

        $deal->update($request->validated());

        return response()->json(['data' => $deal], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact, Deal $deal)
    {
        $deal->delete();

        return response()->json(null, 204);
    }
}
