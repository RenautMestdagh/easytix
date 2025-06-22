<?php

namespace App\Livewire\Modals;

use App\Models\Event;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class EventPickerModal extends Component
{
    public $showModal = false;
    public $search = '';

    #[Modelable]
    public $selectedEventId = null;

    public $selectedEventName = '';
    public $showTriggerButton = true;
    public $maxResults = 15;

    protected $listeners = ['openEventPicker'];

    public function mount($selectedEventId = null, $showTriggerButton = true)
    {
        $this->selectedEventId = $selectedEventId;
        $this->showTriggerButton = $showTriggerButton;

        if ($selectedEventId) {
            $event = Event::withTrashed()->find($selectedEventId);
            $this->selectedEventName = $event ? $event->name : '';
        }
    }

    public function openEventPicker($selectedEventId = null)
    {
        $this->selectedEventId = $selectedEventId;
        if ($selectedEventId) {
            $event = Event::withTrashed()->find($selectedEventId);
            $this->selectedEventName = $event ? $event->name : '';
        }
        $this->showModal = true;
    }

    public function selectEvent($eventId, $eventName)
    {
        $this->selectedEventId = $eventId;
        $this->selectedEventName = $eventName;
        $this->dispatch('eventSelected', $eventId, $eventName);
        $this->showModal = false;
    }

    public function clearSelection()
    {
        $this->selectedEventId = null;
        $this->selectedEventName = '';
        $this->dispatch('eventSelected', null, '');
    }

    public function getEventsProperty()
    {
        return Event::query()
            ->with('venue')
            ->where('organization_id', auth()->user()->organization_id)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('date')
            ->limit($this->maxResults)
            ->get();
    }

    public function getTotalMatchingEventsProperty()
    {
        return Event::query()
            ->where('organization_id', auth()->user()->organization_id)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->count();
    }

    public function render()
    {
        return view('livewire.modals.event-picker-modal');
    }
}
