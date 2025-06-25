<?php

namespace App\Livewire\Backend;

use App\Models\Event;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ShowRevenue extends Component
{
    use WithPagination;

    public $selectedEvent = '';
    public $dateRangeFilter = '';
    public $perPage = 15;
    public $chartType = 'bar';

    protected $listeners = ['eventSelected'];

    protected $queryString = [
        'selectedEvent' => ['except' => ''],
        'dateRangeFilter' => ['except' => ''],
    ];

    public function mount()
    {
        // No need to pass an event now
    }

    public function eventSelected($eventId, $eventName)
    {
        $this->selectedEvent = $eventId;
    }

    public function getDailyRevenueChartProperty()
    {
        $dailyData = DB::table(function($query) {
            $query->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('COALESCE(SUM(ticket_types.price_cents), 0) as total_cents')
            )
                ->from('orders')
                ->join('tickets', 'tickets.order_id', '=', 'orders.id')
                ->join('ticket_types', 'ticket_types.id', '=', 'tickets.ticket_type_id')
                ->join('events', 'events.id', '=', 'ticket_types.event_id')
                ->where('events.organization_id', auth()->user()->organization_id)
                ->when($this->selectedEvent, function ($q) {
                    $q->where('events.id', $this->selectedEvent);
                })
                ->when($this->dateRangeFilter, function ($q) {
                    $dates = explode(' to ', $this->dateRangeFilter);
                    if (count($dates) === 2) {
                        $q->whereBetween('orders.created_at', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
                    }
                })
                ->groupBy(DB::raw('DATE(orders.created_at)'));
        }, 'daily_revenue')
            ->orderBy('date')
            ->get();

        // Rest of the method remains the same
        $chart = LivewireCharts::lineChartModel()
            ->setTitle('Daily Revenue')
            ->setAnimated(true)
            ->withDataLabels(true)
            ->setColors(['#3b82f6']);

        foreach ($dailyData as $data) {
            $chart->addPoint($data->date, $data->total_cents / 100);
        }

        return $chart;
    }

    public function getEventRevenueChartProperty()
    {
        $eventData = DB::table('events')
            ->select(
                'events.id',
                'events.name',
                DB::raw('COALESCE(SUM(ticket_types.price_cents), 0) as total_cents'),
                DB::raw('COUNT(tickets.id) as ticket_count')
            )
            ->leftJoin('ticket_types', 'ticket_types.event_id', '=', 'events.id')
            ->leftJoin('tickets', 'tickets.ticket_type_id', '=', 'ticket_types.id')
            ->leftJoin('orders', 'orders.id', '=', 'tickets.order_id')
            ->where('events.organization_id', auth()->user()->organization_id)
            ->whereNotNull('orders.id')
            ->when($this->selectedEvent, function ($query) {
                $query->where('events.id', $this->selectedEvent);
            })
            ->when($this->dateRangeFilter, function ($query) {
                $dates = explode(' to ', $this->dateRangeFilter);
                if (count($dates) === 2) {
                    $query->whereBetween('orders.created_at', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
                }
            })
            ->groupBy('events.id', 'events.name') // Include all non-aggregated columns
            ->orderByDesc('total_cents')
            ->get();

        $chart = LivewireCharts::columnChartModel()
            ->setTitle('Revenue by Event')
            ->setAnimated(true)
//            ->withOnColumnClickEvent('selectedEvent')
            ->setDataLabelsEnabled(true);

        foreach ($eventData as $data) {
            $chart->addColumn($data->name, $data->total_cents / 100, $this->getRandomColor());
        }

        return $chart;
    }

    public function getTicketTypeRevenueChartProperty()
    {
        $ticketTypeData = DB::table('ticket_types')
            ->select(
                'ticket_types.id',
                'ticket_types.name',
                'events.name as event_name',
                DB::raw('COALESCE(SUM(ticket_types.price_cents), 0) as total_cents'),
                DB::raw('COUNT(tickets.id) as ticket_count')
            )
            ->leftJoin('tickets', 'tickets.ticket_type_id', '=', 'ticket_types.id')
            ->leftJoin('orders', 'orders.id', '=', 'tickets.order_id')
            ->leftJoin('events', 'events.id', '=', 'ticket_types.event_id')
            ->where('events.organization_id', auth()->user()->organization_id)
            ->whereNotNull('orders.id')
            ->when($this->selectedEvent, function ($query) {
                $query->where('events.id', $this->selectedEvent);
            })
            ->when($this->dateRangeFilter, function ($query) {
                $dates = explode(' to ', $this->dateRangeFilter);
                if (count($dates) === 2) {
                    $query->whereBetween('orders.created_at', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
                }
            })
            ->groupBy('ticket_types.id', 'ticket_types.name', 'events.name') // Include all non-aggregated columns
            ->orderByDesc('total_cents')
            ->get();

        $chart = LivewireCharts::pieChartModel()
            ->setTitle('Revenue by Ticket Type')
            ->setAnimated(true)
            ->withDataLabels(true);

        foreach ($ticketTypeData as $data) {
            $label = $this->selectedEvent ? $data->name : "{$data->name} ({$data->event_name})";
            $chart->addSlice($label, $data->total_cents / 100, $this->getRandomColor());
        }

        return $chart;
    }

    public function getRevenueDataProperty()
    {
        return DB::table('orders')
            ->select(
                'orders.id',
                'orders.created_at',
                'customers.first_name',
                'customers.last_name',
                'customers.email',
                'events.name as event_name',
                DB::raw('COALESCE(SUM(ticket_types.price_cents), 0) as total_cents'),
                DB::raw('COUNT(tickets.id) as ticket_count')
            )
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->join('tickets', 'tickets.order_id', '=', 'orders.id')
            ->join('ticket_types', 'ticket_types.id', '=', 'tickets.ticket_type_id')
            ->join('events', 'events.id', '=', 'ticket_types.event_id')
            ->where('events.organization_id', auth()->user()->organization_id)
            ->when($this->selectedEvent, function ($query) {
                $query->where('events.id', $this->selectedEvent);
            })
            ->when($this->dateRangeFilter, function ($query) {
                $dates = explode(' to ', $this->dateRangeFilter);
                if (count($dates) === 2) {
                    $query->whereBetween('orders.created_at', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
                }
            })
            ->groupBy('orders.id', 'orders.created_at', 'customers.first_name', 'customers.last_name', 'customers.email', 'events.name')
            ->orderBy('orders.created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function getTotalRevenueProperty()
    {
        return DB::table('orders')
                ->join('tickets', 'tickets.order_id', '=', 'orders.id')
                ->join('ticket_types', 'ticket_types.id', '=', 'tickets.ticket_type_id')
                ->join('events', 'events.id', '=', 'ticket_types.event_id')
                ->where('events.organization_id', auth()->user()->organization_id)
                ->when($this->selectedEvent, function ($query) {
                    $query->where('events.id', $this->selectedEvent);
                })
                ->when($this->dateRangeFilter, function ($query) {
                    $dates = explode(' to ', $this->dateRangeFilter);
                    if (count($dates) === 2) {
                        $query->whereBetween('orders.created_at', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
                    }
                })
                ->sum('ticket_types.price_cents') / 100;
    }

    public function getTotalTicketsSoldProperty()
    {
        return DB::table('orders')
            ->join('tickets', 'tickets.order_id', '=', 'orders.id')
            ->join('ticket_types', 'ticket_types.id', '=', 'tickets.ticket_type_id')
            ->join('events', 'events.id', '=', 'ticket_types.event_id')
            ->where('events.organization_id', auth()->user()->organization_id)
            ->when($this->selectedEvent, function ($query) {
                $query->where('events.id', $this->selectedEvent);
            })
            ->when($this->dateRangeFilter, function ($query) {
                $dates = explode(' to ', $this->dateRangeFilter);
                if (count($dates) === 2) {
                    $query->whereBetween('orders.created_at', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
                }
            })
            ->count();
    }

    public function getAverageOrderValueProperty()
    {
        $totalOrders = DB::table('orders')
            ->join('tickets', 'tickets.order_id', '=', 'orders.id')
            ->join('ticket_types', 'ticket_types.id', '=', 'tickets.ticket_type_id')
            ->join('events', 'events.id', '=', 'ticket_types.event_id')
            ->where('events.organization_id', auth()->user()->organization_id)
            ->when($this->selectedEvent, function ($query) {
                $query->where('events.id', $this->selectedEvent);
            })
            ->when($this->dateRangeFilter, function ($query) {
                $dates = explode(' to ', $this->dateRangeFilter);
                if (count($dates) === 2) {
                    $query->whereBetween('orders.created_at', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
                }
            })
            ->distinct('orders.id')
            ->count('orders.id');

        return $totalOrders > 0 ? $this->totalRevenue / $totalOrders : 0;
    }

    public function resetFilters()
    {
        $this->reset([
            'selectedEvent',
            'dateRangeFilter'
        ]);
    }

    public function getEventsProperty()
    {
        return Event::where('organization_id', auth()->user()->organization_id)
            ->orderBy('date', 'desc')
            ->get();
    }

    private function getRandomColor()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.backend.revenue', [
            'revenueData' => $this->revenueData,
            'dailyRevenueChart' => $this->dailyRevenueChart,
            'eventRevenueChart' => $this->eventRevenueChart,
            'ticketTypeRevenueChart' => $this->ticketTypeRevenueChart,
            'totalRevenue' => $this->totalRevenue,
            'totalTicketsSold' => $this->totalTicketsSold,
            'averageOrderValue' => $this->averageOrderValue,
            'events' => $this->events,
        ]);
    }
}
