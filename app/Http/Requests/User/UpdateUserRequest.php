<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'user.password' => [
                'nullable',
                'string',
                Password::defaults(),
                'confirmed'
            ],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'user.password.confirmed' => 'The password confirmation does not match.',
        ]);
    }
}
