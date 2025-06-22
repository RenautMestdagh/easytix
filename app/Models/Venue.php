<?php

namespace App\Models;

use App\Models\Scopes\VenueOrganizationScope;
use App\Observers\VenueObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([VenueOrganizationScope::class])]
#[ObservedBy([VenueObserver::class])]
class Venue extends Model
{
    /** @use HasFactory<\Database\Factories\VenueFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'max_capacity',
        'coordinates',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // Venue.php
    public function getGoogleMapsUrl(): string
    {
        if (empty($this->coordinates)) {
            return '';
        }

        return "https://www.google.com/maps/search/?api=1&query={$this->coordinates}";
    }
}
