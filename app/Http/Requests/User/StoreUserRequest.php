<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends UserRequest
{

    protected $organizationId;
    protected $userEmail;
    protected $role;
    public function __construct($organizationId = null, $userEmail = null,  $role = null)
    {
        parent::__construct();
        $this->organizationId = $organizationId;
        $this->userEmail = $userEmail;
        $this->role = $role;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = array_merge(parent::rules(), [
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
            ],
        ]);

        // Only add unique validation if role is not null
        if (!empty($this->role) || session('organization_id')) {
            $rules['userEmail'][] = Rule::unique('users', 'email')->where(function ($query) {
                return $this->organizationId
                    ? $query->where('organization_id', $this->organizationId)
                    : $query->whereNull('organization_id');
            });
        }

        return $rules;
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
