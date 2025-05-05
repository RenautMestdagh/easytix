<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends UserRequest
{
    protected $userId;

    public function __construct($userId = null)
    {
        parent::__construct();
        $this->userId = $userId;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->userId ?? ($this->route('user') ? $this->route('user')->id : $this->route('id'));

        return array_merge(parent::rules(), [
            'userPassword' => [
                'nullable',
                'string',
                Password::defaults(),
                'confirmed'
            ],
            'userEmail' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'userPassword.confirmed' => 'The password confirmation does not match.',
            'userEmail.required' => 'The email field is required.',
            'userEmail.email' => 'The email must be a valid email address.',
            'userEmail.max' => 'The email may not be greater than 255 characters.',
            'userEmail.unique' => 'This email is already in use.',
        ]);
    }
}
