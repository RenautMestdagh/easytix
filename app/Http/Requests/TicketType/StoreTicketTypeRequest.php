<?php

namespace App\Http\Requests\TicketType;

class StoreTicketTypeRequest extends TicketTypeRequest
{

    public function __construct($event = null, $publish_option = null)
    {
        $this->authorizePermission();
        parent::__construct($event, $publish_option);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return parent::rules();
    }

    public function messages(): array
    {
        return parent::messages();
    }
}
