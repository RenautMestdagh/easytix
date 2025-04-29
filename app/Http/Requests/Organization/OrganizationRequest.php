<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organization.name' => ['required', 'string', 'max:255'],
            'organization.subdomain' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9\-]+$/',
                $this->getUniqueSubdomainRule(),
            ],
        ];
    }

    /**
     * Get the unique subdomain validation rule based on the request context.
     *
     * @return \Illuminate\Validation\Rules\Unique
     */
    protected function getUniqueSubdomainRule()
    {
        $rule = Rule::unique('organizations', 'subdomain');

        // If we're updating an existing organization, ignore the current organization's ID
        if ($this->organization && isset($this->organization['id'])) {
            $rule->ignore($this->organization['id']);
        }

        return $rule;
    }

    public function messages(): array
    {
        return [
            'organization.name.required' => 'The organization name is required.',
            'organization.name.max' => 'The organization name may not be greater than 255 characters.',
            'organization.subdomain.required' => 'The subdomain is required.',
            'organization.subdomain.regex' => 'The subdomain may only contain lowercase letters, numbers, and hyphens.',
            'organization.subdomain.unique' => 'This subdomain is already in use. Please choose another one.',
        ];
    }
}
