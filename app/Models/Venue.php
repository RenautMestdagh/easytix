<?php

namespace App\Models;

use App\Models\Scopes\VenueOrganizationScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([VenueOrganizationScope::class])]
class Venue extends Model
{
    /** @use HasFactory<\Database\Factories\VenueFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'organization_id',
        'coordinates',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
