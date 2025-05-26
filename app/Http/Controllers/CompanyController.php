<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCompanyContact;
use App\Http\Requests\PostCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Company",
 *     description="Company Endpoints"
 * )
 */
class CompanyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/companies",
     *     tags={"Company"},
     *     summary="Get list of companies",
     *     description="Returns list of companies",
     *     operationId="getCompanyList",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="withTrashed",
     *         in="query",
     *         description="Include soft deleted records",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/Company")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/v1/companies",
     *     summary="Create a new company",
     *     tags={"Company"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name","domain"},
     *
     *             @OA\Property(property="name", type="string", example="Acme Corp"),
     *             @OA\Property(property="domain", type="string", example="www.acme.com"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Company created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Acme Corp"),
     *                 @OA\Property(property="domain", type="string", example="www.acme.com"),
     *                 @OA\Property(property="created_at", type="string", format="datetime", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
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
