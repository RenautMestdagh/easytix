<?php

namespace App\Http\Requests\User;

use App\Traits\AuthorizesWithPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    use AuthorizesWithPermission;

    protected $role = null;
    protected $organizationId;
    protected $user = null;

    public function __construct($role = null, $organizationId = null, $user = null)
    {
        parent::__construct();
        $this->role = $role;
        $this->organizationId = $organizationId;
        $this->user = $user;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organization_id' => [
                // making sure the user is in an organization (if on subdomain, the user should be in that organization)
                Rule::when($this->role !== 'superadmin', [
                    'required',
                    'exists:organizations,id',
                    session('organization_id') ? 'in:' . session('organization_id') : null,
                ]),
                // making sure a superadmin is not assigned to an organization
                Rule::when($this->role === 'superadmin', [
                    'prohibited',
                ]),
            ],
            'userName' => ['required', 'string', 'max:255'],
            'role' => [
                'required',
                'string',
                'exists:roles,name',
                function ($attribute, $value, $fail) {
                    if($this->user?->hasRole('superadmin') && $value !== 'superadmin') {
                        $fail(__('Superadmin role cannot be changed.'));
                    } else if($this->user && !$this->user->hasRole('superadmin') && $value === 'superadmin') {
                        $fail(__('Normal user cannot become superadmin.'));
                    }
                },
            ],
            'userPassword' => [
                'string',
                Password::defaults(),
                'confirmed',
            ],
            'userEmail' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->where(function ($query) {
                        return $this->organizationId === null
                            ? $query->whereNull('organization_id')
                            : $query->where('organization_id', $this->organizationId);
                    })
                    ->ignore($this->user?->id)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'userName.required' => 'The name field is required.',
            'userName.max' => 'The name may not be greater than 255 characters.',

            'role.required' => 'The role field is required.',
            'role.exists' => 'The selected role is invalid.',

            'organization_id.required' => 'The organization field is required.',
            'organization_id.exists' => 'The selected organization is invalid.',
            'organization_id.prohibited' => 'Superadmin cannot be assigned to an organization.',

            'userPassword.string' => 'The password must be a string.',
            'userPassword.confirmed' => 'The password confirmation does not match.',
            'userPassword.min' => 'The password must be at least 8 characters.',
            'userPassword.mixed' => 'The password must contain both uppercase and lowercase letters.',
            'userPassword.letters' => 'The password must contain at least one letter.',
            'userPassword.numbers' => 'The password must contain at least one number.',
            'userPassword.symbols' => 'The password must contain at least one symbol.',
            'userPassword.uncompromised' => 'The given password has appeared in a data leak. Please choose a different password.',

            'userEmail.required' => 'The email field is required.',
            'userEmail.email' => 'The email must be a valid email address.',
            'userEmail.max' => 'The email may not be greater than 255 characters.',
            'userEmail.unique' => 'This email is already in use.',
        ];
    }
}
