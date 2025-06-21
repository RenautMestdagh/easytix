<?php

namespace App\Models;

use App\Models\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[ScopedBy([OrganizationScope::class])]
class Organization extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'subdomain',
        'logo', // stores filename with extension (e.g. "logo.png")
        'favicon', // stores filename with extension
        'background_image', // stores filename with extension
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function admins()
    {
        return $this->hasMany(User::class)->role('admin');
    }

    public function venues()
    {
        return $this->hasMany(Venue::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function discountCodes()
    {
        return $this->hasMany(DiscountCode::class);
    }

    public function getTicketCountAttribute()
    {
        return Ticket::whereHas('ticketType.event', function($query) {
            $query->where('organization_id', $this->id);
        })->count();
    }

    /**
     * Get the favicon URL.
     */
    public function getFaviconUrlAttribute()
    {
        if (!$this->favicon) return null;
        return Storage::disk('public')->url("organizations/{$this->id}/{$this->favicon}");
    }

    /**
     * Get the logo URL.
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->logo) return null;
        return Storage::disk('public')->url("organizations/{$this->id}/{$this->logo}");
    }

    /**
     * Get the background image URL.
     */
    public function getBackgroundUrlAttribute()
    {
        if (!$this->background_image) return null;
        return Storage::disk('public')->url("organizations/{$this->id}/{$this->background_image}");
    }
}
