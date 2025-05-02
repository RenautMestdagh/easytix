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
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function admins()
    {
        return $this->hasMany(User::class)->role('admin');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    // In your Organization model
    public function getFaviconUrlAttribute()
    {
        $faviconPath = "organizations/{$this->id}/favicon.png";
        $icoPath = "organizations/{$this->id}/favicon.ico";

        if (Storage::disk('public')->exists($faviconPath)) {
            return Storage::disk('public')->url($faviconPath);
        }

        if (Storage::disk('public')->exists($icoPath)) {
            return Storage::disk('public')->url($icoPath);
        }

        return null;
    }

    public function getLogoUrlAttribute()
    {
        $extensions = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
        foreach ($extensions as $extension) {
            $storagePath = "organizations/{$this->id}/logo.{$extension}";
            if (Storage::disk('public')->exists($storagePath)) {
                return Storage::disk('public')->url($storagePath);
            }
        }
        return null;
    }

    public function getBackgroundUrlAttribute()
    {
        $extensions = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
        foreach ($extensions as $extension) {
            $storagePath = "organizations/{$this->id}/background.{$extension}";
            if (Storage::disk('public')->exists($storagePath)) {
                return Storage::disk('public')->url($storagePath);
            }
        }
        return null;
    }

    protected static function booted()
    {
        static::deleting(function ($organization) {
            Storage::disk('public')->deleteDirectory("organizations/{$organization->id}");
        });
    }
}
