<?php

namespace App\Http\Requests\User;

use App\Traits\AuthorizesWithPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    use AuthorizesWithPermission;

    protected $organizationId;
    protected $ignoreId = null;

    public function __construct($organizationId = null, $ignoreId = null)
    {
        parent::__construct();
        $this->organizationId = $organizationId;
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
            'userName' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'organization_id' => [
                'nullable',
                Rule::when(session('organization_id'), [
                    'in:' . session('organization_id'),
                ]),
            ],
            'userPassword' => [
                'string',
                Password::defaults(),
                'confirmed'
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
                    ->ignore($this->ignoreId)
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

            'organization_id.exists' => 'The selected organization is invalid.',

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
