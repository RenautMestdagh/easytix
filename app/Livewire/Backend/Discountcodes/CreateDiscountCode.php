<?php

namespace App\Livewire\Backend\Discountcodes;

use App\Models\DiscountCode;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateDiscountCode extends Component
{
    public ?string $code = null;
    public ?int $event_id = null;
    public ?int $discount_percent = null;
    public ?string $discount_fixed_euros = null;
    public ?int $max_uses = null;
    public string $discount_type = 'percent';

    public function mount()
    {
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

    public function store()
    {
        $validatedData = $this->validate();

        // Convert euros to cents if fixed discount
        $discountFixedCents = null;
        if ($validatedData['discount_type'] === 'fixed' && $validatedData['discount_fixed_euros']) {
            $discountFixedCents = (int) round(str_replace(',', '.', $validatedData['discount_fixed_euros']) * 100);
        }

        $discountCode = new DiscountCode([
            'organization_id' => session('organization_id'),
            'event_id' => $validatedData['event_id'],
            'code' => $validatedData['code'],
            'discount_percent' => $validatedData['discount_type'] === 'percent' ? $validatedData['discount_percent'] : null,
            'discount_fixed_cents' => $validatedData['discount_type'] === 'fixed' ? $discountFixedCents : null,
            'max_uses' => $validatedData['max_uses'],
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
