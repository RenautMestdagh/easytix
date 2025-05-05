<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{

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
            'userName' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'userName.required' => 'The name field is required.',
            'userName.max' => 'The name may not be greater than 255 characters.',
            'role.required' => 'The role field is required.',
            'role.exists' => 'The selected role is invalid.',
            'organization_id.required' => 'Non-superadmin users must belong to an organization.',
            'organization_id.exists' => 'The selected organization is invalid.',
        ];
    }
}
