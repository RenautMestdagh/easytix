<?php

namespace App\Http\Requests\Venue;

class StoreVenueRequest extends VenueRequest
{

    public function __construct()
    {
        $this->authorizePermission();
        parent::__construct();
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
