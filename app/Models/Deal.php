<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Deal",
 *     title="Deal",
 *     description="Deal model",
 *     required={"id", "contact_id","title","amount","currency","status"},
 *
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="contact_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="title", type="string", example="Business Deal"),
 *     @OA\Property(property="amount", type="number", format="float", example=1000.00),
 *     @OA\Property(property="currency", type="string", example="USD"),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="datetime", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="datetime", nullable=true, example=null)
 * )
 */
class Deal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['contact_id', 'title', 'amount', 'currency', 'status'];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
