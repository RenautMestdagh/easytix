<?php

namespace App\Http\Requests\DiscountCode;

use App\Traits\AuthorizesWithPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiscountCodeRequest extends FormRequest
{
    use AuthorizesWithPermission;

    protected $start_date = null;
    protected $ignoreId = null;

    public function __construct($start_date = null, $ignoreId = null)
    {
        parent::__construct();
        $this->start_date = $start_date;
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
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('discount_codes')
                    ->where(function ($query) {
                        return $query->where('organization_id', session('organization_id'));
                    })
                    ->ignore($this->ignoreId)
            ],
            'event_id' => [
                'nullable',
                Rule::exists('events', 'id')->where(function ($query) {
                    return $query->where('organization_id', session('organization_id'));
                }),
            ],
            'start_date' => 'nullable|date',
            'end_date' => [
                'nullable',
                'date',
                Rule::when(
                    $this->start_date !== null,
                    'after_or_equal:start_date'
                ),
            ],
            'max_uses' => 'nullable|integer|min:0|max:4294967295',
            'discount_type' => 'required|in:percent,fixed',
            'discount_percent' => [
                'nullable',
                'required_if:discount_type,percent',
                'prohibited_unless:discount_type,percent',
                'integer',
                'min:1',
                'max:100',
            ],
            'discount_fixed_euros' => [
                'nullable',
                'required_if:discount_type,fixed',
                'prohibited_unless:discount_type,fixed',
                'numeric',
                'min:0.01',
                'max:42949672.95',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'The discount code is required.',
            'code.string' => 'The discount code must be a string.',
            'code.max' => 'The discount code may not be greater than 50 characters.',
            'code.unique' => 'This discount code already exists.',

            'event_id.exists' => 'The selected event does not exist or does not belong to your organization.',

            'start_date.date' => 'The start date must be a valid date.',

            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',

            'max_uses.integer' => 'The maximum uses must be an integer.',
            'max_uses.min' => 'The maximum uses must be at least 1.',
            'max_uses.max' => 'The maximum uses may not be greater than 4294967295.',

            'discount_type.required' => 'The discount type is required.',
            'discount_type.in' => 'The discount type must be either percent or fixed.',

            'discount_percent.required_if' => 'The discount percent is required when discount type is percent.',
            'discount_percent.prohibited_unless' => 'The discount percent should only be provided when discount type is percent.',
            'discount_percent.integer' => 'The discount percent must be an integer.',
            'discount_percent.min' => 'The discount must be at least 1%.',
            'discount_percent.max' => 'The discount may not be greater than 100%.',

            'discount_fixed_euros.required_if' => 'The fixed discount amount is required when discount type is fixed.',
            'discount_fixed_euros.prohibited_unless' => 'The fixed discount amount should only be provided when discount type is fixed.',
            'discount_fixed_euros.numeric' => 'The discount amount must be a number.',
            'discount_fixed_euros.min' => 'The discount amount must be at least €0,01.',
            'discount_fixed_euros.max' => 'The discount amount may not be greater than €42949672,95.',
        ];
    }
}
