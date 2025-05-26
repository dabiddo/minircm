<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCompanyContact;
use App\Http\Requests\PostCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $companies = Company::query();

        if ($request->has('withTrashed') && $request->input('withTrashed') == 'true') {
            $companies->withTrashed();
        }

        return response()->json(['data' => $companies->get()], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostCompanyRequest $request)
    {
        $company = Company::create($request->validated());

        return response()->json(['data' => $company], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return response()->json(['data' => $company], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {

        $company->update($request->validated());

        return response()->json(['data' => $company], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json(null, 204);
    }

    public function attachContact(PostCompanyContact $request, Company $company)
    {
        if ($request->filled('contact_id')) {
            $contact = Contact::find($request->input('contact_id'));

            $contact->company()->associate($company);
            $contact->save();
        } else {
            $validatedData = $request->validated();
            $validatedData['company_id'] = $company->id;
            $contact = Contact::create($validatedData);
        }

        return response()->json($contact, 201);
    }
}
