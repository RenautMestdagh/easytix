<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class EventOrganizationScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($organizationId = session('organization_id')) {
            $builder->where('organization_id', $organizationId);
        }
    }
}

