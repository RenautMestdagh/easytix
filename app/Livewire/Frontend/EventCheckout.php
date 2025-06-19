<?php

namespace App\Livewire\Frontend;

use App\Models\Customer;
use App\Models\DiscountCode;
use App\Traits\NavigateEventCheckout;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Stripe\StripeClient;

class EventCheckout extends Component
{
    use NavigateEventCheckout;

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

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:255',
        'gender' => 'nullable|in:male,female,other,prefer not to say',
        'date_of_birth' => 'nullable|date',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
    ];

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
        if (str_starts_with($propertyName, 'first_name') ||
            str_starts_with($propertyName, 'last_name') ||
            str_starts_with($propertyName, 'email') ||
            str_starts_with($propertyName, 'phone') ||
            str_starts_with($propertyName, 'gender') ||
            str_starts_with($propertyName, 'date_of_birth') ||
            str_starts_with($propertyName, 'address') ||
            str_starts_with($propertyName, 'city') ||
            str_starts_with($propertyName, 'country')
        ) {
            $this->saveCustomerData();
        }
    }

    protected function saveCustomerData()
    {
        if (!$this->tempOrder) return;

        // Validate the data using the same rules defined in the $rules property
        $validatedData = $this->validate();

        $customerData = [
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'gender' => $validatedData['gender'],
            'date_of_birth' => $validatedData['date_of_birth'],
            'address' => $validatedData['address'],
            'city' => $validatedData['city'],
            'country' => $validatedData['country'],
        ];

        $customer = Customer::withoutGlobalScopes()->find($this->tempOrder->customer_id);

        if ($customer) {
            // Customer exists, update it
            $customer->update($customerData);
        } else {
            // No customer yet, create new one
            $customer = Customer::withoutGlobalScopes()->create($customerData);

            // Link new customer to the temp order
            $this->tempOrder->customer_id = $customer->id;
            $this->tempOrder->save();
        }
    }

    public function applyDiscount()
    {
        if($this->tempOrder->checkout_stage !== 1) return;

        $this->resetErrorBag('discountError');
        if (empty($this->discountCode)) {
            $this->addError('discountError', 'Please enter a discount code');
            return;
        }
        // Validate the discount code is not empty
        $this->validate([
            'discountCode' => 'required|string'
        ], [
            'discountCode.required' => 'Please enter a discount code'
        ]);

        // cant do it with normal where because thats case insensitive
        $discount = DiscountCode::whereRaw('BINARY code = ?', [$this->discountCode])
            ->where(function($query) {
                $query->where('event_id', $this->event->id)
                    ->orWhere('organization_id', $this->event->organization_id);
            })
            ->first();

        if (!$discount) {
            $this->addError('discountError', 'Invalid discount code');
            return;
        }

        // Check if code is already applied
        if ($this->tempOrder->discountCodes()->where('code', $this->discountCode)->exists()) {
            $this->addError('discountError', 'Discount code already applied');
            return;
        }

        // Check if new discount type conflicts with existing discounts
        $newDiscountType = $discount->discount_percent ? 'percent' : 'fixed';
        $existingDiscounts = $this->tempOrder->discountCodes()->get();

        if ($existingDiscounts->isNotEmpty()) {
            $existingType = $existingDiscounts->first()->discount_percent ? 'percent' : 'fixed';
            if ($existingType !== $newDiscountType) {
                $this->addError('discountError', 'This discount cannot be combined with already applied discounts.');
                return;
            }
        }

        // Apply discount
        $this->tempOrder->discountCodes()->attach($discount->id);

        // Check max uses
        if ($discount->max_uses && $discount->getAllUsesCount() > $discount->max_uses) {
            $this->tempOrder->discountCodes()->detach($discount->id);
            $this->addError('discountError', 'Discount code has reached maximum uses');
            return;
        }

        $this->discountCode = '';
        $this->loadAppliedDiscounts();
        $this->calculateOrderTotal();
    }

    public function removeDiscount($discountId)
    {
        if($this->tempOrder->checkout_stage !== 1) return;

        $this->tempOrder->discountCodes()->detach($discountId);
        $this->loadAppliedDiscounts();
        $this->calculateOrderTotal();
    }


    public function backToTickets()
    {
        try {
            $this->saveCustomerData();
        } catch (ValidationException $e) {}

        $this->tempOrder->checkout_stage = 0;
        $this->tempOrder->save();
        return redirect()->route('event.tickets', [$this->event->organization->subdomain, $this->event->uniqid]);
    }

    public function proceedToPayment()
    {
        $this->saveCustomerData();

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

        return redirect()->route('event.payment', [$this->event->organization->subdomain, $this->event->uniqid]);
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
