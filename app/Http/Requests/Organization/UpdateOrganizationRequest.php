<?php

namespace App\Http\Requests\Organization;

use Illuminate\Validation\Rule;

class UpdateOrganizationRequest extends OrganizationRequest
{
    protected $organizationId;

    public function __construct($organizationId = null)
    {
        parent::__construct();
        $this->organizationId = $organizationId;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $organizationId = $this->organizationId ?? ($this->route('organization') ? $this->route('organization')->id : $this->route('organization'));

        return array_merge(parent::rules(), [
            'organizationSubdomain' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('organizations','subdomain')->ignore($organizationId),
            ],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'organizationSubdomain.required' => 'The subdomain is required.',
            'organizationSubdomain.regex' => 'The subdomain may only contain lowercase letters, numbers, and hyphens.',
            'organizationSubdomain.unique' => 'This subdomain is already in use. Please choose another one.',
        ]);
    }
}
