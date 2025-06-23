<?php

namespace App\Livewire\Frontend;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\DiscountCode;
use App\Traits\FlashMessage;
use App\Traits\NavigateEventCheckout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Stripe\StripeClient;

class EventCheckout extends Component
{
    use NavigateEventCheckout, FlashMessage;

    // Customer fields
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $gender;
    public $date_of_birth;
    public $address;
    public $city;
    public $country;

    public $discountCode = '';
    public $discountError = '';

    public function boot()
    {
        $this->initialize();
    }

    public function mount($subdomain, $eventuniqid)
    {
        if(!$this->checkCorrectFlow())
            return;
        // Only populate fields if customer exists, otherwise leave blank
        if ($this->tempOrder->customer_id) {
            $customer = Customer::withoutGlobalScopes()->find($this->tempOrder->customer_id);
            if ($customer) {
                $this->first_name = $customer->first_name;
                $this->last_name = $customer->last_name;
                $this->email = $customer->email;
                $this->phone = $customer->phone;
                $this->gender = $customer->gender;
                $this->date_of_birth = $customer->date_of_birth;
                $this->address = $customer->address;
                $this->city = $customer->city;
                $this->country = $customer->country;
            }
        }
    }


    public function updated($propertyName)
    {
        if (in_array(explode('.', $propertyName)[0], [
            'first_name', 'last_name', 'email', 'phone',
            'gender', 'date_of_birth', 'address', 'city', 'country'
        ])) {
            $this->saveSingleCustomerField($propertyName);
        }
    }

    protected function saveSingleCustomerField($propertyName)
    {
        if (!$this->tempOrder) return;

        $field = explode('.', $propertyName)[0];

        if($field == 'gender' && empty($this->gender))
            $this->gender = null;

        $validatedData = $this->validateOnly($field,
            (new CustomerRequest)->rules(),
            (new CustomerRequest)->messages(),
        );

        $customer = Customer::withoutGlobalScopes()->find($this->tempOrder->customer_id);

        try {
            if ($customer) {
                $customer->update([$field => $validatedData[$field]]);
            } else if ($this->first_name && $this->last_name && $this->email && $this->phone) {
                $this->saveAllCustomerData();
            }
        } catch (\Exception $e) {}
    }

    public function saveAllCustomerData()
    {
        if (!$this->tempOrder) return;

        $validatedData = $this->validate(
            (new CustomerRequest)->rules(),
            (new CustomerRequest)->messages(),
        );

        $customer = Customer::withoutGlobalScopes()->find($this->tempOrder->customer_id);

        if ($customer) {
            $customer->update($validatedData);
        } else {
            $customer = Customer::create($validatedData);
            $this->tempOrder->customer_id = $customer->id;
            $this->tempOrder->save();
        }
    }

    public function applyDiscount()
    {
        if ($this->tempOrder->checkout_stage !== 1) return;

        if (empty($this->discountCode)) {
            $this->addError('discountError', 'Please enter a discount code');
            return;
        }

        $this->validate(
            ['discountCode' => 'required|string'],
            ['discountCode.required' => 'Please enter a discount code']
        );

        DB::beginTransaction();

        try {
            $discount = DiscountCode::whereRaw('BINARY code = ?', [$this->discountCode])
                ->where('organization_id', $this->event->organization_id)
                ->where(function ($query) {
                    $query->whereNull('event_id')
                        ->orWhere('event_id', $this->event->id);
                })
                ->where(function ($query) {
                    $query->whereNull('start_date')
                        ->orWhere('start_date', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                })
                ->lockForUpdate()
                ->first();

            if (!$discount) {
                DB::rollBack();
                $this->addError('discountError', 'Invalid discount code');
                return;
            }

            // Check if already applied
            if ($this->tempOrder->discountCodes()->where('code', $this->discountCode)->exists()) {
                DB::rollBack();
                $this->addError('discountError', 'Discount code already applied');
                return;
            }

            $newDiscountType = $discount->discount_percent ? 'percent' : 'fixed';
            $existingDiscounts = $this->tempOrder->discountCodes()->get();

            if ($existingDiscounts->isNotEmpty()) {
                $existingType = $existingDiscounts->first()->discount_percent ? 'percent' : 'fixed';
                if ($existingType !== $newDiscountType) {
                    DB::rollBack();
                    $this->addError('discountError', 'This discount cannot be combined with already applied discounts.');
                    return;
                }
            }

            // Check max uses
            if ($discount->max_uses && $discount->all_orders_count >= $discount->max_uses) {
                DB::rollBack();
                $this->addError('discountError', 'Discount code has reached maximum uses');
                return;
            }

            // Apply the discount safely
            $this->tempOrder->discountCodes()->attach($discount->id);

            DB::commit();

            $this->discountCode = '';
            $this->loadAppliedDiscounts();
            $this->calculateOrderTotal();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error applying discount code: ' . $e->getMessage());
            $this->addError('discountError', 'Something went wrong, please try again.');
        }
    }


    public function removeDiscount($discountId)
    {
        if($this->tempOrder->checkout_stage !== 1) return;

        try {
            $this->tempOrder->discountCodes()->detach($discountId);
        } catch (\Exception $e) {
            Log::error('Error removing discount code: ' . $e->getMessage());
            $this->addError('discountError', 'Something went wrong, please try again.');
        }

        $this->loadAppliedDiscounts();
        $this->calculateOrderTotal();
    }


    public function backToTickets()
    {
        $this->tempOrder->checkout_stage = 0;
        try {
            try {$this->saveAllCustomerData();} catch (\Exception $e) {}
            $this->tempOrder->save();
            redirect()->route('event.tickets', [$this->event->organization->subdomain, $this->event->uniqid]);
        } catch (\Exception $e) {
            Log::error('Error backing to tickets: ' . $e->getMessage());
            $this->flashMessage('An error occurred, please try again.', 'error');
        }
    }

    public function proceedToPayment()
    {
        try {
            $this->saveAllCustomerData();
            $this->calculateOrderTotal();

            if(!$this->tempOrder->payment_id) {
                // Create Stripe payment intent
                $stripe = new StripeClient(config('app.stripe.secret'));
                $paymentIntent = $stripe->paymentIntents->create([
                    'amount' => $this->orderTotal,
                    'currency' => 'eur',
                    'automatic_payment_methods' => ['enabled' => true],
                ]);

                $this->tempOrder->payment_id = $paymentIntent->id;
            }

            $this->tempOrder->checkout_stage = 2;
            $this->tempOrder->save();

            redirect()->route('event.payment', [$this->event->organization->subdomain, $this->event->uniqid]);
        } catch (\Exception $e) {
            Log::error('Error proceeding to payment: ' . $e->getMessage());
            $this->flashMessage('An error occurred, please try again.', 'error');
        }
    }

    public function render()
    {
        return view('livewire.frontend.event-ticketflow')
            ->layout('components.layouts.organization', [
                'backgroundOverride' => $this->event->background_image_url ?? null,
                'logoOverride' => $this->event->header_image_url ?? null
            ]);
    }
}
