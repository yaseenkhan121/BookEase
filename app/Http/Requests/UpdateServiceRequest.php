<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $service = $this->route('service');

        // Senior Logic: Only the owner (Provider) or an Admin can update this service
        return $service && (
            $this->user()->id === $service->provider_id || 
            $this->user()->isAdmin()
        );
    }

    /**
     * Prepare data for validation (Data Sanitization)
     */
    protected function prepareForValidation()
    {
        if ($this->has('price')) {
            // Remove any currency symbols or commas before numeric validation
            $this->merge([
                'price' => str_replace([',', '$', ' '], '', $this->price),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'service_name' => ['sometimes', 'required', 'string', 'max:100'],
            'duration'     => ['sometimes', 'required', 'integer', 'min:5', 'max:480'],
            'price'        => ['sometimes', 'required', 'numeric', 'min:0'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'is_active'    => ['sometimes', 'boolean'], // Allow toggling service visibility
        ];
    }

    /**
     * Custom attribute names for better error messages.
     */
    public function attributes(): array
    {
        return [
            'service_name' => 'service name',
            'duration'     => 'service duration',
            'is_active'    => 'visibility status',
        ];
    }
}