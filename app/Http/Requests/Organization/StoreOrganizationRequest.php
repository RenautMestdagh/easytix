<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends OrganizationRequest
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
        return array_merge(parent::rules(), [
            'user.name' => ['required', 'string', 'max:255'],
            'user.email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'user.password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'user.name.required' => 'The user name is required.',
            'user.email.required' => 'The email address is required.',
            'user.email.email' => 'The email address must be a valid email.',
            'user.email.unique' => 'This email address is already registered.',
            'user.password.required' => 'The password is required.',
            'user.password.min' => 'The password must be at least 8 characters long.',
            'user.password.confirmed' => 'The password confirmation does not match.',
        ]);
    }
}
