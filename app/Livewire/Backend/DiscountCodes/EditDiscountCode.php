<?php

namespace App\Livewire\Backend\DiscountCodes;

use App\Http\Requests\DiscountCode\UpdateDiscountCodeRequest;
use App\Models\DiscountCode;
use App\Models\Event;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EditDiscountCode extends Component
{
    public DiscountCode $discountCode;
    public ?string $code = null;
    public ?int $event_id = null;
    public ?Datetime $start_date = null;
    public ?Datetime $end_date = null;
    public ?int $max_uses = null;
    public string $discount_type = 'percent';
    public ?int $discount_percent = null;
    public ?string $discount_fixed_euros = null;

    public function mount(DiscountCode $discountCode)
    {
        if($discountCode->getAllUsesCount() > 0)
            redirect()->route('discount-codes.index');

        $this->discountCode = $discountCode;

        // Initialize form fields
        $this->code = $discountCode->code;
        $this->event_id = $discountCode->event_id;
        $this->start_date = $discountCode->start_date;
        $this->end_date = $discountCode->end_date;
        $this->max_uses = $discountCode->max_uses;

        // Set discount type and values
        if ($discountCode->discount_percent) {
            $this->discount_type = 'percent';
            $this->discount_percent = $discountCode->discount_percent;
        } else {
            $this->discount_type = 'fixed';
            $this->discount_fixed_euros = number_format($discountCode->discount_fixed_cents / 100, 2);
        }
    }

    public function updated($propertyName)
    {
        $propertyName === 'discount_type' &&
        ($this->discount_type === 'percent'
            ? $this->discount_fixed_euros = null
            : $this->discount_percent = null);

        $fieldRules = (new UpdateDiscountCodeRequest(
            $this->discountCode->id
        ))->rules();
        $fieldMessages = (new UpdateDiscountCodeRequest())->messages();

        if (!array_key_exists($propertyName, $fieldRules)) {
            return; // skip validation if no rule is defined
        }

        $this->validateOnly($propertyName, $fieldRules, $fieldMessages);
    }

    public function update()
    {
        if($this->discountCode->getAllUsesCount() > 0) {
            session()->flash('message', __('Cannot edit used discount code'));
            session()->flash('message_type', 'error');
            return redirect()->route('discount-codes.index');
        }


        $validatedData = $this->validate(
            (new UpdateDiscountCodeRequest(
                $this->discountCode->id,
            ))->rules(),
            (new UpdateDiscountCodeRequest())->messages()
        );

        // Convert euros to cents if fixed discount
        $discountFixedCents = null;
        if ($validatedData['discount_type'] === 'fixed' && $validatedData['discount_fixed_euros']) {
            $discountFixedCents = (int) round($validatedData['discount_fixed_euros'] * 100);
        }

        $updateData = [
            'event_id' => $validatedData['event_id'],
            'code' => $validatedData['code'],
            'discount_percent' => $validatedData['discount_type'] === 'percent' ? $validatedData['discount_percent'] : null,
            'discount_fixed_cents' => $validatedData['discount_type'] === 'fixed' ? $discountFixedCents : null,
            'max_uses' => $validatedData['max_uses'],
        ];

        $this->discountCode->update($updateData);

        session()->flash('message', __('Discount code updated successfully.'));
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
        return view('livewire.discount-codes.edit-discount-code');
    }
}
