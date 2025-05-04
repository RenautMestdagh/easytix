<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'user.name' => ['required', 'string', 'max:255'],
            'user.email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email'.$this->userModel->email,
//                Rule::unique('users', 'email')->ignore($this->user->email),
            ],
            'role' => ['required', 'string', 'exists:roles,name'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'user.name.required' => 'The name field is required.',
            'user.name.max' => 'The name may not be greater than 255 characters.',
            'user.email.required' => 'The email field is required.',
            'user.email.email' => 'The email must be a valid email address.',
            'user.email.max' => 'The email may not be greater than 255 characters.',
            'user.email.unique' => 'This email is already in use.',
            'role.required' => 'The role field is required.',
            'role.exists' => 'The selected role is invalid.',
            'organization_id.required' => 'Non-superadmin users must belong to an organization.',
            'organization_id.exists' => 'The selected organization is invalid.',
        ];
    }
}
