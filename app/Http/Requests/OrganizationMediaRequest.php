<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationMediaRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Set to true to authorize all users
    }

    public function rules()
    {
        return [
            'favicon' => ['nullable', 'image', 'max:1024', 'mimes:png,ico,pdf', 'dimensions:max_width=32,max_height=32'],
            'logo' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp', 'dimensions:max_width=2000,max_height=2000'],
            'background' => ['nullable', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp', 'dimensions:max_width=3000,max_height=1500'],
        ];
    }

    public function messages()
    {
        return [
            'favicon.image' => 'The favicon must be a valid image file.',
            'favicon.max' => 'The favicon may not be greater than 1MB in size.',
            'favicon.mimes' => 'The favicon must be a file of type: png or ico.',
            'favicon.dimensions' => 'The favicon must be exactly 32x32 pixels.',

            'logo.image' => 'The logo must be a valid image file.',
            'logo.max' => 'The logo may not be greater than 2MB in size.',
            'logo.mimes' => 'The logo must be a file of type: jpg, jpeg, png, or webp.',
            'logo.dimensions' => 'The logo dimensions may not exceed 2000x2000 pixels.',

            'background.image' => 'The background must be a valid image file.',
            'background.max' => 'The background may not be greater than 5MB in size.',
            'background.mimes' => 'The background must be a file of type: jpg, jpeg, png, or webp.',
            'background.dimensions' => 'The background dimensions may not exceed 3000x1500 pixels.',
        ];
    }

    public function attributes()
    {
        return [
            'logo' => 'organization logo',
            'background' => 'background image',
            'favicon' => 'favicon',
        ];
    }
}
