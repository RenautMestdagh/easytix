<?php

namespace App\Http\Requests\User;

class UpdateUserRequest extends UserRequest
{
    public function __construct($organizationId = null, $userId = null)
    {
        $this->authorizePermission();
        parent::__construct($organizationId, $userId);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['userPassword'][] = 'nullable';
        return $rules;
    }

    public function messages(): array
    {
        return parent::messages();
    }
}
