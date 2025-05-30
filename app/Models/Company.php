<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Company",
 *     type="object",
 *     title="Company",
 *     required={"id", "name","domain"},
 *
 *     @OA\Property(property="id", type="string", format="uuid", example="f47ac10b-58cc-4372-a567-0e02b2c3d479"),
 *     @OA\Property(property="name", type="string", example="Wayne Tech"),
 *     @OA\Property(property="domain", type="string", example="R&D"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
 * )
 */
class Company extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['name', 'domain'];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
