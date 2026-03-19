<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated customers (or admins) should initiate bookings
        return auth()->check() && !auth()->user()->isProvider();
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Ensure time is trimmed and formatted correctly before validation hits
        if ($this->time) {
            $this->merge([
                'time' => trim($this->time),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'provider_id' => [
                'required',
                'exists:users,id',
                // Senior Logic: A user cannot book an appointment with themselves
                Rule::notIn([auth()->id()]),
            ],
            'service_id'  => [
                'required',
                'exists:services,id'
            ],
            'date'        => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'time'        => [
                'required',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', // HH:MM 24hr format
            ],
            'notes'       => [
                'nullable',
                'string',
                'max:500'
            ],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'provider_id.not_in'  => 'You cannot book an appointment with yourself.',
            'date.after_or_equal' => 'Appointments must be scheduled for today or a future date.',
            'time.regex'          => 'The time must be in a valid 24-hour format (e.g., 14:00).',
            'service_id.exists'   => 'The selected service is invalid or no longer offered.',
        ];
    }
}