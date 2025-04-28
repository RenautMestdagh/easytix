<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TicketTypeOrganizationScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($organizationId = session('organization_id')) {
            $builder->whereHas('event', function (Builder $query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            });
        }
    }
}
