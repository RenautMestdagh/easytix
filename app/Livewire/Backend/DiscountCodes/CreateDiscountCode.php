<?php

namespace App\Livewire\Backend\DiscountCodes;

use App\Http\Requests\DiscountCode\StoreDiscountCodeRequest;
use App\Models\DiscountCode;
use App\Models\Event;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateDiscountCode extends Component
{
    public ?string $code = null;
    public ?int $event_id = null;
    public ?Datetime $start_date = null;
    public ?Datetime $end_date = null;
    public ?int $max_uses = null;
    public string $discount_type = 'percent';
    public ?int $discount_percent = null;
    public ?string $discount_fixed_euros = null;

    public function updated($propertyName)
    {
        $propertyName === 'discount_type' &&
        ($this->discount_type === 'percent'
            ? $this->discount_fixed_euros = null
            : $this->discount_percent = null);

        $fieldRules = (new StoreDiscountCodeRequest())->rules();
        $fieldMessages = (new StoreDiscountCodeRequest())->messages();

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function store()
    {
        $validatedData = $this->validate(
            (new StoreDiscountCodeRequest())->rules(),
            (new StoreDiscountCodeRequest())->messages()
        );

        // Convert euros to cents if fixed discount
        $discountFixedCents = null;
        if ($validatedData['discount_type'] === 'fixed' && $validatedData['discount_fixed_euros']) {
            $discountFixedCents = (int) round($validatedData['discount_fixed_euros'] * 100);
        }

        $discountCode = new DiscountCode([
            'organization_id' => session('organization_id'),
            'event_id' => $validatedData['event_id'],
            'code' => $validatedData['code'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'max_uses' => $validatedData['max_uses'],
            'discount_percent' => $validatedData['discount_type'] === 'percent' ? $validatedData['discount_percent'] : null,
            'discount_fixed_cents' => $validatedData['discount_type'] === 'fixed' ? $discountFixedCents : null,
        ]);

        $discountCode->save();

        session()->flash('message', __('Discount code created successfully.'));
        session()->flash('message_type', 'success');

        return redirect()->route('discount-codes.index');
    }

    public function generateCode()
    {
        $this->code = strtoupper(str()->random(8));
        $this->validateOnly('code');
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
        return view('livewire.discount-codes.create-discount-code');
    }
}
