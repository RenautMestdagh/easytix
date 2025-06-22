<?php

namespace App\Livewire\Backend\DiscountCodes;

use App\Http\Requests\DiscountCode\StoreDiscountCodeRequest;
use App\Models\DiscountCode;
use App\Models\Event;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CreateDiscountCode extends Component
{
    use FlashMessage;

    public ?string $code = null;
    public $event_id = null;
    public $start_date = null;
    public $end_date = null;
    public ?int $max_uses = null;
    public string $discount_type = 'percent';
    public ?int $discount_percent = null;
    public ?string $discount_fixed_euros = null;

    protected $listeners = ['eventSelected'];

    public function updated($propertyName)
    {
        $propertyName === 'discount_type' &&
        ($this->discount_type === 'percent'
            ? $this->discount_fixed_euros = null
            : $this->discount_percent = null);

        $fieldRules = (new StoreDiscountCodeRequest(
            $this->start_date,
        ))->rules();
        $fieldMessages = (new StoreDiscountCodeRequest())->messages();

        if ($propertyName === 'start_date' || $propertyName === 'end_date') {
            $this->validateOnly('start_date', $fieldRules, $fieldMessages);
            $this->validateOnly('end_date', $fieldRules, $fieldMessages);
            return;
        }

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function eventSelected($eventId, $eventName)
    {
        $this->event_id = $eventId;
    }

    public function store()
    {
        $validatedData = $this->validate(
            (new StoreDiscountCodeRequest(
                $this->start_date,
            ))->rules(),
            (new StoreDiscountCodeRequest())->messages(),
        );

        // Convert euros to cents if fixed discount
        $discountFixedCents = null;
        if ($validatedData['discount_type'] === 'fixed' && $validatedData['discount_fixed_euros']) {
            $discountFixedCents = (int) round($validatedData['discount_fixed_euros'] * 100);
        }

        try {
            DiscountCode::create([
                'organization_id' => session('organization_id'),
                'event_id' => $validatedData['event_id'],
                'code' => $validatedData['code'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'max_uses' => $validatedData['max_uses'],
                'discount_percent' => $validatedData['discount_type'] === 'percent' ? $validatedData['discount_percent'] : null,
                'discount_fixed_cents' => $validatedData['discount_type'] === 'fixed' ? $discountFixedCents : null,
            ]);

            $this->flashMessage('Discount code added successfully.');
            redirect()->route('discount-codes.index');
        } catch (\Exception $e) {
            $this->flashMessage('Error while creating discount code.', 'error');
            Log::error('Error creating discount code: ' . $e->getMessage());
        }
    }

    public function generateCode()
    {
        $this->code = strtoupper(str()->random(8));
        $this->validateOnly(
            'code',
            (new StoreDiscountCodeRequest())->rules(),
            (new StoreDiscountCodeRequest())->messages(),
        );
    }

    public function getEventsProperty()
    {
        return Event::where('organization_id', Auth::user()->organization_id)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->get();
    }

    public function render()
    {
        return view('livewire.backend.discount-codes.create-discount-code');
    }
}
