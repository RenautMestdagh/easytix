<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organizationName' => ['required', 'string', 'max:255'],

        ];
    }

    public function messages(): array
    {
        return [
            'organizationName.required' => 'The organization name is required.',
            'organizationName.max' => 'The organization name may not be greater than 255 characters.',
        ];
    }
}
