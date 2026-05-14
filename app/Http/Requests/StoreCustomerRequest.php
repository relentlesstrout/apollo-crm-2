<?php

namespace App\Http\Requests;

use App\DTOs\Customer\CustomerData;
use App\Models\Customer;
use App\Rules\UkPhoneNumber;
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
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', new UkPhoneNumber],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:customers'],
            'invite_to_portal' => ['boolean'],
        ];
    }

    public function toDTO(): CustomerData
    {
        return new CustomerData(
            name: $this->string('name')->toString(),
            phone: $this->string('phone')->toString(),
            email: $this->input('email'),
            inviteToPortal: $this->boolean('invite_to_portal'),
        );
    }
}
