<?php

namespace App\Http\Requests;

use App\DTOs\Property\PropertyData;
use App\Models\Customer;
use App\Models\Property;
use App\Rules\UkPostcode;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Property::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'house' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'postcode' => ['required', 'string', new UkPostcode],
            'notes' => ['nullable', 'string', 'max:255']
        ];
    }

    public function toDTO(): PropertyData
    {
        return new PropertyData(
            house: $this->string('house')->toString(),
            street: $this->string('street')->toString(),
            area: $this->string('area')->toString(),
            postcode: $this->string('postcode')->toString(),
            notes: $this->string('notes')->toString(),
        );
    }
}
