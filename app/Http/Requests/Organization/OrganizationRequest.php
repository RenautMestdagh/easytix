<?php

namespace App\Http\Requests\Organization;

use App\Traits\AuthorizesWithPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganizationRequest extends FormRequest
{
    use AuthorizesWithPermission;

    protected $ignoreId = null;

    public function __construct($ignoreId = null)
    {
        parent::__construct();
        $this->ignoreId = $ignoreId;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organizationName' => ['required', 'string', 'max:255'],
            'organizationSubdomain' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('organizations', 'subdomain')->ignore($this->ignoreId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'organizationName.required' => 'The organization name is required.',
            'organizationName.max' => 'The organization name may not be greater than 255 characters.',
            'organizationSubdomain.required' => 'The subdomain is required.',
            'organizationSubdomain.regex' => 'The subdomain may only contain lowercase letters, numbers, and hyphens.',
            'organizationSubdomain.unique' => 'This subdomain is already in use. Please choose another one.',
        ];
    }
}
