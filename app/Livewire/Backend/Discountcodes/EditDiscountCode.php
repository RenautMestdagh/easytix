<?php

namespace App\Livewire\Backend\Discountcodes;

use App\Models\DiscountCode;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EditDiscountCode extends Component
{
    public DiscountCode $discountCode;
    public ?string $code = null;
    public ?int $event_id = null;
    public ?int $discount_percent = null;
    public ?string $discount_fixed_euros = null;
    public ?int $max_uses = null;
    public string $discount_type = 'percent';

    public function mount(DiscountCode $discountCode)
    {
        $this->discountCode = $discountCode;

        // Initialize form fields
        $this->code = $discountCode->code;
        $this->event_id = $discountCode->event_id;
        $this->max_uses = $discountCode->max_uses;

        // Set discount type and values
        if ($discountCode->discount_percent) {
            $this->discount_type = 'percent';
            $this->discount_percent = $discountCode->discount_percent;
        } else {
            $this->discount_type = 'fixed';
            $this->discount_fixed_euros = number_format($discountCode->discount_fixed_cents / 100, 2);
        }

        $this->authorize('discount-codes.update', $discountCode);
    }

    protected function rules()
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    $exists = DiscountCode::where('organization_id', session('organization_id'))
                        ->where('code', $value)
                        ->where('id', '!=', $this->discountCode->id) // Exclude current discount code
                        ->exists();

                    if ($exists) {
                        $fail(__('This discount code already exists.'));
                    }
                },
            ],
            'event_id' => 'nullable|exists:events,id',
            'discount_percent' => [
                'nullable',
                'required_if:discount_type,percent',
                'integer',
                'min:1',
                'max:100',
            ],
            'discount_fixed_euros' => [
                'nullable',
                'required_if:discount_type,fixed',
                function ($attribute, $value, $fail) {
                    $normalizedValue = str_replace(',', '.', $value);

                    if (!is_numeric($normalizedValue)) {
                        $fail(__('The fixed discount must be a valid number.'));
                        return;
                    }

                    if ($normalizedValue <= 0) {
                        $fail(__('The fixed discount must be greater than 0.'));
                    }
                },
            ],
            'max_uses' => 'nullable|integer|min:1',
            'discount_type' => 'required|in:percent,fixed',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('discount-codes.update', $this->discountCode);
        $validatedData = $this->validate();

        // Convert euros to cents if fixed discount
        $discountFixedCents = null;
        if ($this->discount_type === 'fixed' && $this->discount_fixed_euros) {
            $discountFixedCents = (int) round(str_replace(',', '.', $this->discount_fixed_euros) * 100);
        }

        $updateData = [
            'event_id' => $this->event_id,
            'code' => $this->code,
            'discount_percent' => $this->discount_type === 'percent' ? $this->discount_percent : null,
            'discount_fixed_cents' => $this->discount_type === 'fixed' ? $discountFixedCents : null,
            'max_uses' => $this->max_uses,
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
