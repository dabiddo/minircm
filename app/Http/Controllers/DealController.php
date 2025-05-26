<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostDealRequest;
use App\Http\Requests\UpdateDealRequest;
use App\Models\Deal;
use App\Services\DealsService;
use Illuminate\Http\Request;

class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DealsService $dealsService)
    {
        $deals = $dealsService->searchAndFilter($request);

        return response()->json(['data' => $deals], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostDealRequest $request)
    {
        $validatedData = $request->validated();
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
    public function update(UpdateDealRequest $request, Deal $deal)
    {
        $deal->update($request->validated());

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
