<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends OrganizationRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $organizationId = $this->route('organization') ? $this->route('organization')->id : null;

        return array_merge(parent::rules(), [
            'organization.subdomain' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9\-]+$/',
                $organizationId ? 'unique:organizations,subdomain,' . $organizationId : 'unique:organizations,subdomain',
            ],
            'users_to_remove' => ['array', 'nullable'],
            'users_to_remove.*' => ['exists:users,id', 'different:user_id'],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'organization.subdomain.unique' => 'This subdomain is already taken.',
            'users_to_remove.*.exists' => 'Some users to remove do not exist.',
            'users_to_remove.*.different' => 'You cannot remove the current user from the organization.',
        ]);
    }
}
