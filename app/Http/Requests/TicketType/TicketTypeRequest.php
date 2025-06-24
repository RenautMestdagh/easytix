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
                'min:0',
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
                function ($attribute, $value, $fail) {
                    if($this->event->capacity === null)
                        return;

                    $ticketTypes = $this->event->ticketTypes;
                    if ($this->currentTicketType !== null)
                        $ticketTypes = $ticketTypes->where('id', '!=', $this->currentTicketType->id);
                    $alreadyAvailableTickets = $ticketTypes->sum('available_quantity');

                    if ($value > $this->event->capacity - $alreadyAvailableTickets) {
                        $fail(__('The available quantity must not exceed the event capacity.'));
                    }
                },
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
        return [];
    }
}
