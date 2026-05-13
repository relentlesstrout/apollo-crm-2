<?php

namespace App\Http\Requests;

use App\DTOs\Customer\CustomerData;
use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Rules\UkPhoneNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('customer')) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Customer $customer */
        $customer = $this->route('customer');

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', new UkPhoneNumber],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('customers')->ignore($customer->id)],
            'status' => ['required', Rule::enum(CustomerStatus::class)],
        ];
    }

    public function toDTO(): CustomerData
    {
        return new CustomerData(
            name: $this->string('name')->toString(),
            phone: $this->string('phone')->toString(),
            email: $this->input('email'),
            status: CustomerStatus::from($this->input('status')),
        );
    }
}
