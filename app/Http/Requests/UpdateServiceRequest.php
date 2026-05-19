<?php

namespace App\Http\Requests;

use App\DTOs\Service\ServiceData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function toDTO(): ServiceData
    {
        return new ServiceData(
            name: $this->string('name')->toString(),
            description: $this->string('description')->toString() ?: null,
        );
    }
}
