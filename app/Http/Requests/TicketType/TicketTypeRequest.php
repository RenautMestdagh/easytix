<?php

namespace App\Http\Requests\TicketType;

use App\Traits\AuthorizesWithPermission;
use Illuminate\Foundation\Http\FormRequest;

class TicketTypeRequest extends FormRequest
{
    use AuthorizesWithPermission;

    protected $event;
    protected $publish_option;
    protected $currentTicketType;
    public function __construct($event = null, $publish_option = null, $currentTicketType = null)
    {
        parent::__construct();
        $this->event = $event;
        $this->publish_option = $publish_option;
        $this->currentTicketType = $currentTicketType;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price_euros' => [
                'required',
                'numeric',
                'min:0.01',
                'max:42949672.95',
                function ($attribute, $value, $fail) {
                    if (str_contains($value, ',')) {
                        $fail('Please use a dot (.) as decimal separator.');
                    }
                },
            ],
            'available_quantity' => [
                'nullable',
                'integer',
                'min:1',
                'max:4294967295',
//                function ($attribute, $value, $fail) {
//                    if($this->event->capacity === null)
//                        return;
//
//                    $ticketTypes = $this->event->ticketTypes;
//                    if ($this->currentTicketType !== null)
//                        $ticketTypes = $ticketTypes->where('id', '!=', $this->currentTicketType->id);
//                    $alreadyAvailableTickets = $ticketTypes->sum('available_quantity');
//
//                    if ($value > $this->event->capacity - $alreadyAvailableTickets) {
//                        $fail(__('The available quantity must not exceed the event capacity.'));
//                    }
//                },
            ],
            'publish_option' => 'required|in:publish_now,schedule,draft,with_event',
            'publish_at' => [
                'nullable',
                'required_if:publish_option,schedule',
                'prohibited_unless:publish_option,schedule',
                'date',
                'after:now',
            ],
            'publish_with_event' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The ticket type name is required.',
            'name.string' => 'The ticket type name must be a string.',
            'name.max' => 'The ticket type name may not be greater than 255 characters.',

            'price_euros.required' => 'The price is required.',
            'price_euros.numeric' => 'The price must be a number.',
            'price_euros.min' => 'The price must be at least €0,01.',
            'price_euros.max' => 'The price may not be greater than €42949672,95.',

            'available_quantity.integer' => 'The available quantity must be an integer.',
            'available_quantity.min' => 'The available quantity must be at least 1.',
            'available_quantity.max' => 'The available quantity may not be greater than 4294967295.',

            'publish_option.required' => 'The publish option is required.',
            'publish_option.in' => 'The selected publish option is invalid.',

            'publish_at.required_if' => 'The publish date is required when scheduling.',
            'publish_at.prohibited_unless' => 'The publish date should only be provided when scheduling.',
            'publish_at.date' => 'The publish date must be a valid date.',
            'publish_at.after' => 'The publish date must be in the future.',

            'publish_with_event.boolean' => 'The publish with event field must be true or false.',
        ];
    }
}
