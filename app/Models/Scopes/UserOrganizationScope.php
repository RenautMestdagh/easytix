<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserOrganizationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (request()->routeIs('login-as.use')) {
            return;
        }

        if ($organizationId = session('organization_id')) {
            $builder->where('organization_id', $organizationId);
        }
    }
}
