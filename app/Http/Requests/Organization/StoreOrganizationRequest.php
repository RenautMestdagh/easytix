<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrganizationRequest extends OrganizationRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'organizationSubdomain' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9\-]+$/',
                'unique'
            ],
            'userName' => ['required', 'string', 'max:255'],
            'userEmail' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'userPassword' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'organizationSubdomain.required' => 'The subdomain is required.',
            'organizationSubdomain.regex' => 'The subdomain may only contain lowercase letters, numbers, and hyphens.',
            'userName.required' => 'The user name is required.',
            'userEmail.required' => 'The email address is required.',
            'userEmail.email' => 'The email address must be a valid email.',
            'userEmail.unique' => 'This email address is already registered.',
            'userPassword.required' => 'The password is required.',
            'userPassword.min' => 'The password must be at least 8 characters long.',
            'userPassword.confirmed' => 'The password confirmation does not match.',
        ]);
    }
}
