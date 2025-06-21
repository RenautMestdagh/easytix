<?php

namespace App\Http\Requests\DiscountCode;

class StoreDiscountCodeRequest extends DiscountCodeRequest
{

    public function __construct($start_date = null)
    {
        $this->authorizePermission();
        parent::__construct($start_date);
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
