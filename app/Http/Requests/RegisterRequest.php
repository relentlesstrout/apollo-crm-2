<?php

namespace App\Http\Requests;

use App\DTOs\User\UserData;
use App\Enums\UserRole;
use App\Models\Invitation;
use App\Rules\UkPhoneNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', new UkPhoneNumber],
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function toDTO(): UserData
    {
        return new UserData(
            name: $this->input('name'),
            phone: $this->input('phone'),
            email: $this->input('email'),
            password: $this->input('password'),
            role: UserRole::from($this->input('role')),
        );
    }
}
