<?php

namespace App\Http\Requests;

use App\Enums\PropertyStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyStatusRequest extends FormRequest
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
            'status' => ['required', Rule::enum(PropertyStatus::class)],
        ];
    }

    public function status(): PropertyStatus
    {
        return PropertyStatus::from($this->input('status'));
    }
}
