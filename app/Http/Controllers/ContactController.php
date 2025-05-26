<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Contact",
 *     description="Contact Endpoints"
 * )
 */
class ContactController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/contacts",
     *     summary="Get list of contacts",
     *     description="Returns paginated list of contacts with optional search and filter",
     *     operationId="getContacts",
     *     tags={"Contact"},
     *     security={{ "bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Filter contacts by email",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="email")
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
     *                 @OA\Items(ref="#/components/schemas/Contact")
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
     *     )
     * )
     */
    public function index(Request $request, ContactService $contactService)
    {
        $contacts = $contactService->searchAndFilter($request);

        return response()->json(['data' => $contacts], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/contacts",
     *     summary="Create a new contact",
     *     description="Creates a new contact with the provided information",
     *     operationId="storeContact",
     *     tags={"Contact"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"company_id","first_name", "last_name", "email", "phone_number"},
     *
     *             @OA\Property(property="company_id", type="integer", example=1),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="phone_number", type="string", example="+1234567890"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Contact created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                 @OA\Property(property="phone_number", type="string", example="+1234567890"),
     *                 @OA\Property(property="created_at", type="string", format="datetime", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2023-01-01T00:00:00Z")
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
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
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
    public function show(Contact $contact)
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
    public function destroy(Contact $contact)
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
