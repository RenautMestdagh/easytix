<?php

namespace App\Http\Requests\Organization;

class UpdateOrganizationRequest extends OrganizationRequest
{

    public function __construct($ignoreId = null)
    {
        $this->authorizePermission();
        parent::__construct($ignoreId);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return parent::rules();
    }

    public function messages(): array
    {
        return parent::messages();
    }
}
