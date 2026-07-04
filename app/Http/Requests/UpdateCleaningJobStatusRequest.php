<?php

namespace App\Http\Requests;

use App\Enums\CleaningJobStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCleaningJobStatusRequest extends FormRequest
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
            'status' => ['required', Rule::enum(CleaningJobStatus::class)],
        ];
    }

    public function status(): CleaningJobStatus
    {
        return CleaningJobStatus::from($this->input('status'));
    }
}
