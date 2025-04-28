<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organization.name' => ['required', 'string', 'max:255'],
            'organization.subdomain' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9\-]+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'organization.name.required' => 'The organization name is required.',
            'organization.name.max' => 'The organization name may not be greater than 255 characters.',
            'organization.subdomain.required' => 'The subdomain is required.',
            'organization.subdomain.regex' => 'The subdomain may only contain lowercase letters, numbers, and hyphens.',
        ];
    }
}
