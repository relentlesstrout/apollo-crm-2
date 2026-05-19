<?php

namespace App\Http\Requests;

use App\DTOs\PropertyService\PropertyServiceData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'price' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after:effective_from'],
        ];
    }

    public function toDTO(): PropertyServiceData
    {
        return new PropertyServiceData(
            serviceId: $this->integer('service_id'),
            price: $this->integer('price'),
            description: $this->string('description')->toString() ?: null,
            effectiveFrom: $this->string('effective_from')->toString(),
            effectiveTo: $this->string('effective_to')->toString() ?: null,
        );
    }
}
