<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        return $this->users()
            ->whereHas('roles', fn ($query) => $query->where('name', 'admin'));
    }

    public function lastAdminId(): ?int
    {
        $adminIds = $this->admins()->pluck('id');

        return $adminIds->count() === 1 ? $adminIds->first() : null;
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function removeUser(User $user)
    {
        // Soft delete the user instead of removing them
        $user->delete();
    }
}
