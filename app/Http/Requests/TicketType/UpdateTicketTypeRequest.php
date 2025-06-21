<?php

namespace App\Http\Requests\TicketType;

class UpdateTicketTypeRequest extends TicketTypeRequest
{

    public function __construct($event = null, $publish_option = null, $currentTicketType = null)
    {
        $this->authorizePermission();
        parent::__construct($event, $publish_option, $currentTicketType);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['price_euros'][] = function ($attribute, $value, $fail) {
            $preventAmountChange = $this->currentTicketType->is_published || $this->currentTicketType->tickets->count() !== 0;
            if ($preventAmountChange && (int) round($value * 100) !== $this->currentTicketType->price_cents) {
                $fail(__('The price can only be changed if the ticket type is not published and no tickets have been bought.'));
            }
        };

        return $rules;
    }

    public function messages(): array
    {
        return parent::messages();
    }
}
