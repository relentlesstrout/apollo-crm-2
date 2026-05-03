<?php

namespace App\Http\Requests;

use App\DTOs\User\UserData;
use App\Enums\UserRole;
use App\Models\User;
use App\Rules\UkPhoneNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', new UkPhoneNumber],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::enum(UserRole::class) ],
        ];
    }

    public function toDTO(): UserData
    {
        return new UserData(
            name: $this->input('name'),
            phone: $this->input('phone'),
            email: $this->input('email'),
            password: $this->input('password'),
            role: $this->input('role'),
        );
    }
}
