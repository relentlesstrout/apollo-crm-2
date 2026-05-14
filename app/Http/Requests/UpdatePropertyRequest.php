<?php

namespace App\Http\Requests;

use App\DTOs\Property\PropertyData;
use App\Models\Customer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'house' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'area' => 'nullable|string|max:255',
            'postcode' => 'required|string|max:255',
            'notes' => 'nullable|string|max:255',
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
