<?php

namespace App\Http\Requests\Event;

class StoreEventRequest extends EventRequest
{

    public function __construct($publish_option = null, $date = null)
    {
        $this->authorizePermission();
        parent::__construct($publish_option, $date);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['date'][] = 'after:now';
        return $rules;
    }

    public function messages(): array
    {
        return array_merge(
            parent::messages(),
            [
                'date.after' => 'The event date must be a date in the future.',
            ]
        );
    }
}
