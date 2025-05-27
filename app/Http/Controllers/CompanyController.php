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
     *                 @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
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
     * @OA\Get(
     *     path="/api/v1/companies/{company}",
     *     summary="Get a specific company by ID",
     *     description="Returns a single company's details",
     *     operationId="showCompany",
     *     tags={"Company"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="company",
     *         in="path",
     *         description="ID of company to return",
     *         required=true,
     *
     *         @OA\Schema(
     *            type="string",
     *            example="f47ac10b-58cc-4372-a567-0e02b2c3d479"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/Company")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Company not found"
     *     )
     * )
     */
    public function show(Company $company)
    {
        return response()->json(['data' => $company], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/companies/{company}",
     *     summary="Update a company",
     *     description="Updates an existing company's information",
     *     operationId="updateCompany",
     *     tags={"Company"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="company",
     *         in="path",
     *         required=true,
     *         description="ID of company to update",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="name", type="string", example="Acme Corp"),
     *             @OA\Property(property="domain", type="string", example="www.acme.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Company updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *                 @OA\Property(property="name", type="string", example="Acme Corp"),
     *                 @OA\Property(property="domain", type="string", example="www.acme.com"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {

        $company->update($request->validated());

        return response()->json(['data' => $company], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/companies/{company}",
     *     summary="Soft delete a company",
     *     description="Soft deletes the specified company record",
     *     operationId="deleteCompany",
     *     tags={"Company"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="company",
     *         in="path",
     *         description="ID of company to soft delete",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Company successfully deleted"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company not found"
     *     )
     * )
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/company/{company}/contact",
     *     summary="Attach new or existing contact to a company",
     *     tags={"Company"},
     *
     *     @OA\Parameter(
     *         name="company",
     *         in="path",
     *         required=true,
     *         description="Company ID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Either provide an existing contact_id or create a new contact with full details",
     *
     *         @OA\JsonContent(
     *             oneOf={
     *
     *                 @OA\Schema(
     *                     type="object",
     *                     required={"contact_id"},
     *                     properties={
     *
     *                         @OA\Property(property="contact_id", type="string", description="ID of existing contact", example="123e4567-e89b-12d3-a456-426614174000")
     *                     }
     *                 ),
     *
     *                 @OA\Schema(
     *                     type="object",
     *                     required={"first_name", "last_name", "email", "phone_number"},
     *                     properties={
     *
     *                         @OA\Property(property="first_name", type="string", description="Contact's first name", example="John"),
     *                         @OA\Property(property="last_name", type="string", description="Contact's last name", example="Doe"),
     *                         @OA\Property(property="email", type="string", format="email", description="Contact's email address", example="jdoe@example.com"),
     *                         @OA\Property(property="phone_number", type="string", description="Contact's phone number", example="+12345677")
     *                     }
     *                 )
     *             }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Contact successfully attached",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="company_id", type="integer")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Company or Contact not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
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
