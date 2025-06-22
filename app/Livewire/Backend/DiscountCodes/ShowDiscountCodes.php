<?php

namespace App\Livewire\Backend\DiscountCodes;

use App\Models\DiscountCode;
use App\Models\Event;
use App\Models\Organization;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ShowDiscountCodes extends Component
{
    use WithPagination, FlashMessage;

    public $includeDeleted = false;
    public $search = '';
    public $selectedEvent = '';
    public $statusFilter = 'all';
    public $sortField = 'code';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public function getDiscountCodesProperty()
    {
        return DiscountCode::query()
            ->with(['event', 'organization'])
            ->withCount('orders')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhereHas('event', function ($q) {
                            $q->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->selectedEvent, function ($query) {
                $query->where('event_id', $this->selectedEvent);
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->whereNull('deleted_at')
                    ->where(function ($q) {
                        $q->where(function ($subQ) {
                            $subQ->whereNull('max_uses')
                                ->orWhere(function ($q) {
                                    $q->whereNotNull('max_uses')
                                        ->havingRaw('orders_count < max_uses');
                                });
                        })
                            ->where(function ($subQ) {
                                $subQ->where(function ($q) {
                                    $q->whereNull('start_date')
                                        ->orWhere('start_date', '<=', now());
                                })
                                    ->where(function ($q) {
                                        $q->whereNull('end_date')
                                            ->orWhere('end_date', '>=', now());
                                    });
                            })
                            ->where(function ($subQ) {
                                $subQ->whereNull('event_id')
                                    ->orWhereHas('event', function ($q) {
                                        $q->where('date', '>=', now()->format('Y-m-d'));
                                    });
                            });
                    })
                    ->havingRaw('max_uses IS NULL OR orders_count < max_uses')
                    ->groupBy('discount_codes.id');
            })
            ->when($this->statusFilter === 'event_past', function ($query) {
                $query->whereHas('event', function ($q) {
                    $q->where('date', '<', now()->format('Y-m-d'));
                })->whereNull('deleted_at');
            })
            ->when($this->statusFilter === 'expired', function ($query) {
                $query->whereNull('deleted_at')
                    ->where(function ($q) {
                        $q->whereNotNull('end_date')
                            ->where('end_date', '<', now());
                    });
            })
            ->when($this->statusFilter === 'upcoming', function ($query) {
                $query->whereNull('deleted_at')
                    ->whereNotNull('start_date')
                    ->where('start_date', '>', now());
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
        return Event::where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
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
        try {
            DiscountCode::findOrFail($id)->delete();
            $this->flashMessage('Discount code deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting discount code: ' . $e->getMessage());
            $this->flashMessage('Error deleting discount code.', 'error');
        }
    }

    public function forceDeleteDiscountCode($id)
    {
        $this->authorize('discount-codes.delete');

        $canDelete = true;
        try {
            DB::statement('LOCK TABLES discount_code_order WRITE');
            $discountCode = DiscountCode::withTrashed()->findOrFail($id);

            if ($discountCode->orders()->count() > 0) {
                $this->flashMessage('Cannot permanently delete discount code that has been used.', 'error');
                $canDelete = false;
            }

            if ($canDelete) {
                $discountCode->forceDelete();
                $this->flashMessage('Discount code deleted successfully.');
            }
        } catch (\Exception $e) {
            Log::error('An error occurred while force deleting discount code: ' . $e->getMessage());
            $this->flashMessage('An error occurred while deleting discount code', 'error');
        } finally {
            DB::statement('UNLOCK TABLES');
        }
    }

    public function restoreDiscountCode($id)
    {
        $this->authorize('discount-codes.delete');

        try {
            DiscountCode::withTrashed()->findOrFail($id)->restore();
            $this->flashMessage('Discount code restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring discount code: ' . $e->getMessage());
            $this->flashMessage('Error restoring discount code.', 'error');
        }
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
