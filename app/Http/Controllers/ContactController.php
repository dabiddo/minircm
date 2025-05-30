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
     *     description="Returns list of contacts with optional search and filter",
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
     *             @OA\Property(property="company_id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
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
     *                 @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
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
     * @OA\Get(
     *     path="/api/v1/contact/{contact}",
     *     summary="Get contact details by ID",
     *     description="Returns a single contact's details",
     *     operationId="showContact",
     *     tags={"Contact"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="contact",
     *         in="path",
     *         description="ID of contact to return",
     *         required=true,
     *
     *        @OA\Schema(
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
     *             type="object",
     *
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Contact"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found"
     *     )
     * )
     */
    public function show(Contact $contact)
    {
        return response()->json(['data' => $contact], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/contacts/{contact}",
     *     summary="Update a contact",
     *     description="Updates an existing contact's information",
     *     operationId="updateContact",
     *     tags={"Contact"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="contact",
     *         in="path",
     *         description="ID of contact to update",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"company_id","first_name", "last_name", "email"},
     *
     *             @OA\Property(property="company_id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="phone_number", type="string", example="1234567890"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Contact updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="phone_number", type="string", example="1234567890"),
     *             @OA\Property(property="created_at", type="string", format="datetime", example="2023-01-01T00:00:00.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="datetime", example="2023-01-01T00:00:00.000000Z")
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
     *         description="Contact not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $contact->update($request->validated());

        return response()->json($contact, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/contacts/{contact}",
     *     summary="Delete a contact",
     *     description="Deletes a specific contact",
     *     operationId="deleteContact",
     *     tags={"Contact"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="contact",
     *         in="path",
     *         description="ID of contact to delete",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Contact deleted successfully"
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
     *         description="Contact not found"
     *     )
     * )
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
