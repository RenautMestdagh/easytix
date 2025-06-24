<?php

namespace App\Http\Requests\Venue;

use App\Traits\AuthorizesWithPermission;
use Illuminate\Foundation\Http\FormRequest;

class VenueRequest extends FormRequest
{
    use AuthorizesWithPermission;

    public function __construct()
    {
        parent::__construct();
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
            'max_capacity' => 'nullable|integer|min:1|max:4294967295',
            'latitude' => 'required_with:longitude|numeric|between:-90,90',
            'longitude' => 'required_with:latitude|numeric|between:-180,180',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The venue name is required.',
            'name.string' => 'The venue name must be a string.',
            'name.max' => 'The venue name may not be greater than 255 characters.',

            'max_capacity.integer' => 'The maximum capacity must be an integer.',
            'max_capacity.min' => 'The maximum capacity must be at least 1.',
            'max_capacity.max' => 'The maximum capacity may not be greater than 4294967295.',

            'latitude.required_with' => 'The latitude is required when longitude is present.',
            'latitude.numeric' => 'The latitude must be a number.',
            'latitude.between' => 'The latitude must be between -90 and 90 degrees.',

            'longitude.required_with' => 'The longitude is required when latitude is present.',
            'longitude.numeric' => 'The longitude must be a number.',
            'longitude.between' => 'The longitude must be between -180 and 180 degrees.',
        ];
    }
}
