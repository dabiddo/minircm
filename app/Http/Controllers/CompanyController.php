<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Company::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostCompanyRequest $request)
    {
        $company = Company::create($request->validated());

        return response()->json($company, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return response()->json($company, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {

        $company->update($request->validated());

        return response()->json($company, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json(null, 204);
    }
}
