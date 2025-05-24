<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use Illuminate\Http\Request;

class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['data' => Deal::all()], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'contact_id' => 'required|integer|exists:contacts,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'status' => 'required|string|in:open,closed-won,closed-lost',
        ]);

        $deal = Deal::create($validatedData);

        return response()->json(['data' => $deal], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Deal $deal)
    {
        return response()->json(['data' => $deal], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deal $deal)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|max:3',
            'status' => 'sometimes|string|in:open,closed-won,closed-lost',
        ]);

        $deal->update($validatedData);

        return response()->json(['data' => $deal], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deal $deal)
    {
        $deal->delete();

        return response()->json(null, 204);
    }
}
