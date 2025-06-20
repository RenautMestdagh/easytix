<?php

namespace App\Livewire\Backend\Discountcodes;

use App\Models\DiscountCode;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ShowDiscountCodes extends Component
{
    use WithPagination;

    public $includeDeleted = false;
    public $search = '';
    public $selectedEvent = '';
    public $statusFilter = 'all';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    public function mount()
    {
    }

    public function getDiscountCodesProperty()
    {
        return DiscountCode::query()
            ->with(['event', 'organization'])
            ->withCount('orders')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhereHas('event', function($q) {
                            $q->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->selectedEvent, function ($query) {
                $query->where('event_id', $this->selectedEvent);
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->whereNull('deleted_at')
                    ->where(function($q) {
                        $q->where(function($subQ) {
                            $subQ->whereNull('max_uses')
                                ->orWhere(function($q) {
                                    $q->whereNotNull('max_uses')
                                        ->havingRaw('orders_count < max_uses');
                                });
                        })
                            ->where(function($subQ) {
                                $subQ->whereNull('event_id')
                                    ->orWhereHas('event', function($q) {
                                        $q->where('date', '>=', now()->format('Y-m-d'));
                                    });
                            });
                    })
                    ->havingRaw('max_uses IS NULL OR orders_count < max_uses')
                    ->groupBy('discount_codes.id');
            })
            ->when($this->statusFilter === 'event_past', function ($query) {
                $query->whereHas('event', function($q) {
                    $q->where('date', '<', now()->format('Y-m-d'));
                })->whereNull('deleted_at');
            })
            ->when($this->statusFilter === 'limit_reached', function ($query) {
                $query->whereNotNull('max_uses')
                    ->havingRaw('orders_count >= max_uses')
                    ->whereNull('deleted_at')
                    ->groupBy('discount_codes.id');
            })
            ->when($this->statusFilter === 'deleted', function ($query) {
                $query->onlyTrashed();
            })
            ->when($this->includeDeleted && $this->statusFilter !== 'deleted', function ($query) {
                $query->withTrashed();
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function getEventsProperty()
    {
        return Event::orderBy('date')
            ->get();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedEvent()
    {
        $this->resetPage();
    }

    public function updatedIncludeDeleted()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function deleteDiscountCode($id)
    {
        $this->authorize('discount-codes.delete');
        DB::transaction(function () use ($id) {
            $discountCode = DiscountCode::findOrFail($id);
            $discountCode->delete();

            session()->flash('message', __('Discount code deleted successfully.'));
            $this->dispatch('flash-message');
        });
    }

    public function forceDeleteDiscountCode($id)
    {
        $this->authorize('discount-codes.delete');

        $discountCode = DiscountCode::withTrashed()->findOrFail($id);

        if ($discountCode->orders()->count() > 0) {
            session()->flash('message', __('Cannot permanently delete discount code that has been used.'));
            session()->flash('message_type', 'error');
            $this->dispatch('flash-message');
            return;
        }

        $discountCode->forceDelete();

        session()->flash('message', __('Discount code permanently deleted.'));
        $this->dispatch('flash-message');
    }

    public function restoreDiscountCode($id)
    {
        $this->authorize('discount-codes.delete');

        $discountCode = DiscountCode::withTrashed()->findOrFail($id);
        $discountCode->restore();

        session()->flash('message', 'Discount code restored successfully.');
        $this->dispatch('flash-message');
    }

    public function render()
    {
        return view('livewire.discount-codes.show-discount-codes', [
            'discountCodes' => $this->discountCodes,
            'organizations' => Organization::all(),
            'events' => $this->events,
        ]);
    }
}
