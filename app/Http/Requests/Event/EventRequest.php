<?php

namespace App\Http\Requests\Event;

use App\Traits\AuthorizesWithPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
{
    use AuthorizesWithPermission;

    protected $publish_option;
    protected $date;

    public function __construct($publish_option = null, $date = null)
    {
        parent::__construct();
        $this->publish_option = $publish_option;
        $this->date = $date;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'string',
            'venue_id' => [
                'nullable',
                Rule::exists('venues', 'id')->where(function ($query) {
                    return $query->where('organization_id', session('organization_id'));
                }),
            ],
            'use_venue_capacity' => 'boolean',
            'date' => ['required', 'date'],
            'event_image' => 'nullable|image|max:2048',
            'header_image' => 'nullable|image|max:2048',
            'background_image' => 'nullable|image|max:5120',
            'max_capacity' => ['nullable', 'integer', 'min:1'],
            'publish_option' => 'required|in:publish_now,schedule,unlisted',
            'publish_at' => [
                'nullable',
                'required_if:publish_option,schedule',
                'prohibited_unless:publish_option,schedule',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    if ($this->publish_option === 'schedule' && $value >= $this->date) {
                        $fail(__('The publish date must be before the event date.'));
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The event name is required.',
            'name.string' => 'The event name must be a string.',
            'name.max' => 'The event name may not be greater than 255 characters.',

            'description.string' => 'The description must be a string.',

            'venue_id.exists' => 'The selected venue does not exist.',

            'date.required' => 'The event date is required.',
            'date.date' => 'The event date must be a valid date.',

            'event_image.image' => 'The event image must be an image file.',
            'event_image.max' => 'The event image may not be larger than 2MB.',

            'header_image.image' => 'The header image must be an image file.',
            'header_image.max' => 'The header image may not be larger than 2MB.',

            'background_image.image' => 'The background image must be an image file.',
            'background_image.max' => 'The background image may not be larger than 5MB.',

            'max_capacity.integer' => 'The maximum capacity must be an integer.',
            'max_capacity.min' => 'The maximum capacity must be at least 1.',

            'publish_option.required' => 'The publish option is required.',
            'publish_option.in' => 'The publish option must be one of: publish now, schedule, or unlisted.',

            'publish_at.required_if' => 'The publish date is required when scheduling.',
            'publish_at.date' => 'The publish date must be a valid date.',
            'publish_at.after' => 'The publish date must be in the future.',
            'publish_at.custom' => 'The publish date must be before the event date.',
        ];
    }
}
