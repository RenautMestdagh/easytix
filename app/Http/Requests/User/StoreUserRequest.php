<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends UserRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'userPassword' => [
                'required',
                'string',
                Password::defaults(),
                'confirmed'
            ],
            'userEmail' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'userPassword.required' => 'The password field is required.',
            'userPassword.confirmed' => 'The password confirmation does not match.',
            'userEmail.required' => 'The email field is required.',
            'userEmail.email' => 'The email must be a valid email address.',
            'userEmail.max' => 'The email may not be greater than 255 characters.',
            'userEmail.unique' => 'This email is already in use.',
        ]);
    }
}
