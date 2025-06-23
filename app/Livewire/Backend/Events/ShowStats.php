<?php

namespace App\Livewire\Backend\Events;

use App\Models\Customer;
use App\Models\Event;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ShowStats extends Component
{
    use WithPagination;

    public Event $event;
    public $genderFilter = '';
    public $ageRangeFilter = '';
    public $countryFilter = '';
    public $cityFilter = '';
    public $ticketTypeFilter = '';
    public $perPage = 15;
    public $chartType = 'bar';

    protected $queryString = [
        'genderFilter' => ['except' => ''],
        'ageRangeFilter' => ['except' => ''],
        'countryFilter' => ['except' => ''],
        'cityFilter' => ['except' => ''],
        'ticketTypeFilter' => ['except' => ''],
    ];

    public function mount(Event $event)
    {
        $this->event = $event->load('organization');
    }

    public function getCustomersProperty()
    {
        return Customer::query()
            ->select('customers.*')
            ->join('orders', 'orders.customer_id', '=', 'customers.id')
            ->join('tickets', 'tickets.order_id', '=', 'orders.id')
            ->join('ticket_types', 'ticket_types.id', '=', 'tickets.ticket_type_id')
            ->where('ticket_types.event_id', $this->event->id)
            ->when($this->genderFilter, function ($query) {
                $query->where('gender', $this->genderFilter);
            })
            ->when($this->ageRangeFilter, function ($query) {
                $query->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE())'),
                    explode('-', $this->ageRangeFilter));
            })
            ->when($this->countryFilter, function ($query) {
                $query->where('country', $this->countryFilter);
            })
            ->when($this->cityFilter, function ($query) {
                $query->where('city', $this->cityFilter);
            })
            ->when($this->ticketTypeFilter, function ($query) {
                $query->where('ticket_types.id', $this->ticketTypeFilter);
            })
            ->with(['orders.tickets.ticketType'])
            ->groupBy('customers.id')
            ->paginate($this->perPage);
    }

    public function getGenderDistribution()
    {
        return Customer::query()
            ->select('gender', DB::raw('count(*) as total'))
            ->join('orders', 'orders.customer_id', '=', 'customers.id')
            ->join('tickets', 'tickets.order_id', '=', 'orders.id')
            ->join('ticket_types', 'ticket_types.id', '=', 'tickets.ticket_type_id')
            ->where('ticket_types.event_id', $this->event->id)
            ->whereNotNull('gender')
            ->when($this->genderFilter, function ($query) {
                $query->where('gender', $this->genderFilter);
            })
            ->when($this->ageRangeFilter, function ($query) {
                $query->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE())'),
                    explode('-', $this->ageRangeFilter));
            })
            ->when($this->countryFilter, function ($query) {
                $query->where('country', $this->countryFilter);
            })
            ->when($this->cityFilter, function ($query) {
                $query->where('city', $this->cityFilter);
            })
            ->when($this->ticketTypeFilter, function ($query) {
                $query->where('ticket_types.id', $this->ticketTypeFilter);
            })
            ->groupBy('gender')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->gender => $item->total];
            });
    }

    public function getAgeDistribution()
    {
        return Customer::query()
            ->select(DB::raw('FLOOR(TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) / 10) * 10 as decade'),
                DB::raw('count(*) as total'))
            ->join('orders', 'orders.customer_id', '=', 'customers.id')
            ->join('tickets', 'tickets.order_id', '=', 'orders.id')
            ->join('ticket_types', 'ticket_types.id', '=', 'tickets.ticket_type_id')
            ->where('ticket_types.event_id', $this->event->id)
            ->whereNotNull('date_of_birth')
            ->when($this->genderFilter, function ($query) {
                $query->where('gender', $this->genderFilter);
            })
            ->when($this->ageRangeFilter, function ($query) {
                $query->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE())'),
                    explode('-', $this->ageRangeFilter));
            })
            ->when($this->countryFilter, function ($query) {
                $query->where('country', $this->countryFilter);
            })
            ->when($this->cityFilter, function ($query) {
                $query->where('city', $this->cityFilter);
            })
            ->when($this->ticketTypeFilter, function ($query) {
                $query->where('ticket_types.id', $this->ticketTypeFilter);
            })
            ->groupBy('decade')
            ->orderBy('decade')
            ->get()
            ->mapWithKeys(function ($item) {
                $range = $item->decade . '-' . ($item->decade + 9);
                return [$range => $item->total];
            });
    }

    public function getCountryDistribution()
    {
        return Customer::query()
            ->select('country', DB::raw('count(*) as total'))
            ->join('orders', 'orders.customer_id', '=', 'customers.id')
            ->join('tickets', 'tickets.order_id', '=', 'orders.id')
            ->join('ticket_types', 'ticket_types.id', '=', 'tickets.ticket_type_id')
            ->where('ticket_types.event_id', $this->event->id)
            ->whereNotNull('country')
            ->when($this->genderFilter, function ($query) {
                $query->where('gender', $this->genderFilter);
            })
            ->when($this->ageRangeFilter, function ($query) {
                $query->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE())'),
                    explode('-', $this->ageRangeFilter));
            })
            ->when($this->countryFilter, function ($query) {
                $query->where('country', $this->countryFilter);
            })
            ->when($this->cityFilter, function ($query) {
                $query->where('city', $this->cityFilter);
            })
            ->when($this->ticketTypeFilter, function ($query) {
                $query->where('ticket_types.id', $this->ticketTypeFilter);
            })
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->country => $item->total];
            });
    }

    public function getTicketTypeDistribution()
    {
        return DB::table('ticket_types')
            ->select('ticket_types.name', DB::raw('count(tickets.id) as total'))
            ->join('tickets', 'tickets.ticket_type_id', '=', 'ticket_types.id')
            ->join('orders', 'orders.id', '=', 'tickets.order_id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('ticket_types.event_id', $this->event->id)
            ->when($this->genderFilter, function ($query) {
                $query->where('customers.gender', $this->genderFilter);
            })
            ->when($this->ageRangeFilter, function ($query) {
                $query->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, customers.date_of_birth, CURDATE())'),
                    explode('-', $this->ageRangeFilter));
            })
            ->when($this->countryFilter, function ($query) {
                $query->where('customers.country', $this->countryFilter);
            })
            ->when($this->cityFilter, function ($query) {
                $query->where('customers.city', $this->cityFilter);
            })
            ->when($this->ticketTypeFilter, function ($query) {
                $query->where('ticket_types.id', $this->ticketTypeFilter);
            })
            ->groupBy('ticket_types.id', 'ticket_types.name')
            ->orderByDesc('total')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->name => $item->total];
            });
    }

    public function getGenderChartProperty()
    {
        $chart = LivewireCharts::pieChartModel()
            ->setTitle($this->genderFilter ? 'Gender (Filtered: '.ucfirst($this->genderFilter).')' : 'Gender Distribution')
            ->setAnimated(true)
            ->withOnSliceClickEvent('genderFilter')
            ->setDataLabelsEnabled(true);

        $genderData = $this->getGenderDistribution();

        if ($this->genderFilter && $genderData->isEmpty()) {
            $chart->addSlice(ucfirst($this->genderFilter), 1, $this->getGenderColor($this->genderFilter));
        } else {
            foreach ($genderData as $gender => $count) {
                $chart->addSlice(ucfirst($gender), $count, $this->getGenderColor($gender));
            }
        }

        return $chart;
    }

    public function getAgeChartProperty()
    {
        $chart = LivewireCharts::columnChartModel()
            ->setTitle($this->ageRangeFilter ? 'Age Distribution (Filtered: '.$this->ageRangeFilter.')' : 'Age Distribution')
            ->setAnimated(true)
//            ->withOnSliceClickEvent('ageRangeFilter')
            ->setDataLabelsEnabled(true);

        $ageData = $this->getAgeDistribution();

        if ($this->ageRangeFilter && $ageData->isEmpty()) {
            $range = $this->ageRangeFilter;
            $chart->addColumn($range, 1, $this->getAgeColor($range));
        } else {
            foreach ($ageData as $range => $count) {
                $chart->addColumn($range, $count, $this->getAgeColor($range));
            }
        }

        return $chart;
    }

    public function getCountryChartProperty()
    {
        $chart = LivewireCharts::pieChartModel()
            ->setTitle($this->countryFilter ? 'Countries (Filtered: '.$this->countryFilter.')' : 'Top Countries')
            ->setAnimated(true)
            ->withOnSliceClickEvent('countryFilter')
            ->setDataLabelsEnabled(true);

        $countryData = $this->getCountryDistribution();

        if ($this->countryFilter && $countryData->isEmpty()) {
            $chart->addSlice($this->countryFilter, 1, $this->getCountryColor($this->countryFilter));
        } else {
            foreach ($countryData as $country => $count) {
                $chart->addSlice($country, $count, $this->getCountryColor($country));
            }
        }

        return $chart;
    }

    public function getTicketTypeChartProperty()
    {
        $chart = LivewireCharts::pieChartModel()
            ->setTitle($this->ticketTypeFilter ? 'Ticket Types (Filtered)' : 'Ticket Types')
            ->setAnimated(true)
            ->withOnSliceClickEvent('ticketTypeFilter')
            ->setDataLabelsEnabled(true);

        $ticketTypeData = $this->getTicketTypeDistribution();

        if ($this->ticketTypeFilter && $ticketTypeData->isEmpty()) {
            $ticketTypeName = $this->event->ticketTypes->firstWhere('id', $this->ticketTypeFilter)?->name ?? 'Selected Type';
            $chart->addSlice($ticketTypeName, 1, $this->getRandomColor());
        } else {
            foreach ($ticketTypeData as $type => $count) {
                $chart->addSlice($type, $count, $this->getRandomColor());
            }
        }

        return $chart;
    }

    public function resetFilters()
    {
        $this->reset([
            'genderFilter',
            'ageRangeFilter',
            'countryFilter',
            'cityFilter',
            'ticketTypeFilter'
        ]);
    }

    public function render()
    {
        return view('livewire.backend.events.show-stats', [
            'customers' => $this->customers,
            'genderChart' => $this->genderChart,
            'ageChart' => $this->ageChart,
            'countryChart' => $this->countryChart,
            'ticketTypeChart' => $this->ticketTypeChart,
            'ticketTypes' => $this->event->ticketTypes,
            'cities' => Customer::query()
                ->select('city')
                ->join('orders', 'orders.customer_id', '=', 'customers.id')
                ->join('tickets', 'tickets.order_id', '=', 'orders.id')
                ->join('ticket_types', 'ticket_types.id', '=', 'tickets.ticket_type_id')
                ->where('ticket_types.event_id', $this->event->id)
                ->whereNotNull('city')
                ->groupBy('city')
                ->orderBy('city')
                ->pluck('city'),
            'countries' => Customer::query()
                ->select('country')
                ->join('orders', 'orders.customer_id', '=', 'customers.id')
                ->join('tickets', 'tickets.order_id', '=', 'orders.id')
                ->join('ticket_types', 'ticket_types.id', '=', 'tickets.ticket_type_id')
                ->where('ticket_types.event_id', $this->event->id)
                ->whereNotNull('country')
                ->groupBy('country')
                ->orderBy('country')
                ->pluck('country'),
        ]);
    }

    private function getGenderColor($gender)
    {
        $colors = [
            'male' => '#3b82f6',
            'female' => '#ec4899',
            'other' => '#8b5cf6',
            'prefer not to say' => '#64748b'
        ];
        return $colors[$gender] ?? '#94a3b8';
    }

    private function getAgeColor($range)
    {
        $colors = [
            '10-19' => '#93c5fd',
            '20-29' => '#60a5fa',
            '30-39' => '#3b82f6',
            '40-49' => '#2563eb',
            '50-59' => '#1d4ed8',
            '60-69' => '#1e40af',
            '70-79' => '#1e3a8a',
        ];
        return $colors[$range] ?? '#bfdbfe';
    }

    private function getCountryColor($country)
    {
        $colors = [
            'Germany' => '#000000',
            'France' => '#0055A4',
            'Spain' => '#AA151B',
            'Italy' => '#008C45',
            'Netherlands' => '#AE1C28',
            'USA' => '#B22234',
            'UK' => '#012169',
        ];
        return $colors[$country] ?? $this->getRandomColor();
    }

    private function getRandomColor()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}
