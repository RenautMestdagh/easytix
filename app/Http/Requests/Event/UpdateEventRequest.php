<?php

namespace App\Http\Requests\Event;

class UpdateEventRequest extends EventRequest
{

    protected $event;
    public function __construct($publish_option = null, $date = null, $event = null)
    {
        $this->authorizePermission();
        parent::__construct($publish_option, $date);
        $this->event = $event;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $parentRules = parent::rules();

        $parentRules['date'][] = function ($attribute, $value, $fail) {
            if ($value !== $this->event->date->format('Y-m-d\TH:i') && strtotime($value) <= time()) {
                $fail(__('The event date must be in the future.'));
            }
        };

//        $parentRules['max_capacity'][] = function ($attribute, $value, $fail) {
//            $publishedTickets = $this->event->ticketTypes->sum('available_quantity');
//            if ($value !== null && $value < $publishedTickets) {
//                $fail(__("The maximum capacity must be greater than or equal to the number of published tickets ($publishedTickets)."));
//            }
//        };

        return $parentRules;
    }

    public function messages(): array
    {
        return parent::messages();
    }
}
