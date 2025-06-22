<?php

namespace App\Livewire\Modals;

use App\Models\Venue;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class VenuePickerModal extends Component
{
    public $showModal = false;
    public $search = '';

    #[Modelable]
    public $selectedVenueId = null;

    public $selectedVenueName = '';
    public $showTriggerButton = true;
    public $maxResults = 15; // Added max results limit

    protected $listeners = ['openVenuePicker'];

    public function mount($selectedVenueId = null, $showTriggerButton = true)
    {
        $this->selectedVenueId = $selectedVenueId;
        $this->showTriggerButton = $showTriggerButton;

        if ($selectedVenueId) {
            $venue = Venue::withTrashed()->find($selectedVenueId);
            $this->selectedVenueName = $venue ? $venue->name : '';
        }
    }

    public function openVenuePicker($selectedVenueId = null)
    {
        $this->selectedVenueId = $selectedVenueId;
        if ($selectedVenueId) {
            $venue = Venue::withTrashed()->find($selectedVenueId);
            $this->selectedVenueName = $venue ? $venue->name : '';
        }
        $this->showModal = true;
    }

    public function selectVenue($venueId, $venueName)
    {
        $this->selectedVenueId = $venueId;
        $this->selectedVenueName = $venueName;
        $this->dispatch('venueSelected', $venueId, $venueName);
        $this->showModal = false;
    }

    public function clearSelection()
    {
        $this->selectedVenueId = null;
        $this->selectedVenueName = '';
        $this->dispatch('venueSelected', null, '');
    }

    public function getVenuesProperty()
    {
        return Venue::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->limit($this->maxResults)
            ->get();
    }

    public function getTotalMatchingVenuesProperty()
    {
        return Venue::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->count();
    }

    public function render()
    {
        return view('livewire.modals.venue-picker-modal');
    }
}
