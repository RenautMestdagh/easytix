<?php

namespace App\Models;

use App\Models\Scopes\CustomerOrganizationScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([CustomerOrganizationScope::class])]
class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'city',
        'country',
    ];

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }


    /**
     * Get the orders for the customer.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function temporaryOrders()
    {
        return $this->hasMany(TemporaryOrder::class);
    }
}
