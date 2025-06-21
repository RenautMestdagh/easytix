<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\User\StoreUserRequest;

class StoreOrganizationRequest extends OrganizationRequest
{

    protected $userRequest;

    public function __construct()
    {
        $this->authorizePermission();
        parent::__construct();
        $this->userRequest = new StoreUserRequest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userRules = $this->userRequest->rules();
        $rules = array_merge(
            parent::rules(),
            [
                'userName' => $userRules['userName'],
                'userEmail' => $userRules['userEmail'],
                'userPassword' => $userRules['userPassword'],
            ]
        );

        return $rules;
    }

    public function messages(): array
    {
        return array_merge(
            parent::messages(),
            $this->userRequest->messages(),
        );
    }
}
