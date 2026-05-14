<?php

namespace App\Http\Requests;

use App\DTOs\Customer\CustomerData;
use App\DTOs\Property\PropertyData;
use App\Models\Customer;
use App\Rules\UkPhoneNumber;
use App\Rules\UkPostcode;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Customer::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //Customer fields
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', new UkPhoneNumber],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:customers'],
            'invite_to_portal' => ['boolean'],

            //Property fields
            'house' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'postcode' => ['required', 'string', new UkPostcode],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toCustomerDTO(): CustomerData
    {
        return new CustomerData(
            name: $this->string('name')->toString(),
            phone: $this->string('phone')->toString(),
            email: $this->input('email'),
            inviteToPortal: $this->boolean('invite_to_portal'),
        );
    }

    public function toPropertyDTO(): PropertyData
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
