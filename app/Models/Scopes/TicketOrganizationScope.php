<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TicketOrganizationScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($organizationId = session('organization_id')) {
            $builder->whereHas('ticketType.event', function (Builder $query) use ($organizationId) {
                $query->withTrashed() // Include soft-deleted events in the check
                ->where('organization_id', $organizationId);
            });
        }
    }
}
