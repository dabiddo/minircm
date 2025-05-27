<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostDealRequest;
use App\Http\Requests\UpdateDealRequest;
use App\Models\Deal;
use App\Services\DealsService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Deal",
 *     description="Deal Endpoints"
 * )
 */
class DealController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/deals",
     *     summary="Get list of deals",
     *     description="Returns list of deals with optional status filter",
     *     operationId="getDeals",
     *     tags={"Deal"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter deals by status",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="string",
     *             enum={"open", "closed-won", "closed-lost"}
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
     *                 @OA\Items(ref="#/components/schemas/Deal")
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
    public function index(Request $request, DealsService $dealsService)
    {
        $deals = $dealsService->searchAndFilter($request);

        return response()->json(['data' => $deals], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/deals",
     *     summary="Create a new deal",
     *     description="Creates a new deal with the provided data",
     *     tags={"Deal"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"title", "amount", "contact_id", "status"},
     *
     *             @OA\Property(property="contact_id", type="integer", example=1, description="Reference to Contact model"),
     *             @OA\Property(property="title", type="string", example="New Business Deal"),
     *             @OA\Property(property="amount", type="number", format="float", example=10000.00),
     *             @OA\Property(property="currencly", type="string", example="USD"),
     *             @OA\Property(property="status", type="string", enum={"open", "closed-won", "closed-lost"}, example="open"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Deal created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object",
     *                 ref="#/components/schemas/Deal"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(PostDealRequest $request)
    {
        $validatedData = $request->validated();
        $deal = Deal::create($validatedData);

        return response()->json(['data' => $deal], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/deals/{deal}",
     *     summary="Display a specific deal",
     *     description="Retrieves a deal by its ID",
     *     operationId="showDeal",
     *     tags={"Deal"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="deal",
     *         in="path",
     *         description="ID of the deal",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", ref="#/components/schemas/Deal")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Deal not found"
     *     )
     * )
     */
    public function show(Deal $deal)
    {
        return response()->json(['data' => $deal], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/deals/{deal}",
     *     summary="Update a deal",
     *     description="Update an existing deal's information",
     *     operationId="updateDeal",
     *     tags={"Deal"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="deal",
     *         in="path",
     *         description="ID of deal to update",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *          @OA\JsonContent(
     *         required={"contact_id","title", "amount", "currency", "status"},
     *
     *         @OA\Property(property="contact_id", type="integer", example=1),
     *         @OA\Property(property="title", type="string", example="New Business Deal"),
     *         @OA\Property(property="amount", type="number", example=1000.00),
     *         @OA\Property(property="currency", type="string", example="USD"),
     *         @OA\Property(property="status", type="string", example="open"),
     *      ),

     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Deal updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Deal"
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
     *         description="Deal not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(UpdateDealRequest $request, Deal $deal)
    {
        $deal->update($request->validated());

        return response()->json(['data' => $deal], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/deals/{deal}",
     *     summary="Delete a deal",
     *     description="Deletes an existing deal",
     *     operationId="deleteDeal",
     *     tags={"Deal"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="deal",
     *         in="path",
     *         description="ID of deal to delete",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Deal deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Deal not found"
     *     )
     * )
     */
    public function destroy(Deal $deal)
    {
        $deal->delete();

        return response()->json(null, 204);
    }
}
