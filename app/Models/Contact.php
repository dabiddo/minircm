<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Contact",
 *     required={"company_id", "first_name", "last_name", "email","phone_number"},
 *
 *     @OA\Property(property="id", type="integer", format="int64", readOnly="true", example=1),
 *     @OA\Property(property="company_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="first_name", type="string", maxLength=255, example="John"),
 *     @OA\Property(property="last_name", type="string", maxLength=255, example="Doe"),
 *     @OA\Property(property="email", type="string", format="email", maxLength=255, example="john.doe@example.com"),
 *     @OA\Property(property="phone_number", type="string", maxLength=255, example="+1234567890"),
 *     @OA\Property(property="created_at", type="string", format="datetime", readOnly="true"),
 *     @OA\Property(property="updated_at", type="string", format="datetime", readOnly="true"),
 *     @OA\Property(property="deleted_at", type="string", format="datetime", nullable="true")
 * )
 */
class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['company_id', 'first_name', 'last_name', 'email', 'phone_number'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }
}
