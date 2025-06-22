<?php

namespace App\Http\Requests\User;

class StoreUserRequest extends UserRequest
{
    public function __construct($role = null, $organizationId = null)
    {
        $this->authorizePermission();
        parent::__construct($role, $organizationId);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['userPassword'][] = ['required'];
        return $rules;
    }

    public function messages(): array
    {
        return array_merge(
            parent::messages(),
            [
                'userPassword.required' => 'The password field is required.',
            ]
        );
    }
}
