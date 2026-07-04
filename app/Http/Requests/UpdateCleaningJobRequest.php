<?php

namespace App\Http\Requests;

use App\DTOs\CleaningJob\CleaningJobData;
use App\Enums\UserRole;
use App\Models\CleaningJob;
use App\Models\Property;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCleaningJobRequest extends FormRequest
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
            'scheduled_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['integer', Rule::in($this->property()->propertyServices()->pluck('service_id')->all())],
            'prices' => ['required', 'array'],
            'prices.*' => ['numeric', 'min:0.01'],
            'cleaners' => ['array'],
            'cleaners.*' => ['integer', Rule::exists('users', 'id')->where('role', UserRole::Cleaner->value)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ((array) $this->input('services', []) as $serviceId) {
                if (! is_numeric($this->input("prices.{$serviceId}"))) {
                    $validator->errors()->add("prices.{$serviceId}", 'A price is required for each selected service.');
                }
            }
        });
    }

    public function toDTO(): CleaningJobData
    {
        $services = collect($this->input('services', []))
            ->map(fn (int|string $serviceId): array => [
                'service_id' => (int) $serviceId,
                'price' => (int) round((float) $this->input("prices.{$serviceId}") * 100),
            ])
            ->all();

        return new CleaningJobData(
            scheduledAt: $this->string('scheduled_at')->toString(),
            notes: $this->string('notes')->toString() ?: null,
            services: $services,
            cleanerIds: array_map('intval', $this->input('cleaners', [])),
        );
    }

    public function property(): Property
    {
        /** @var CleaningJob $cleaningJob */
        $cleaningJob = $this->route('cleaning_job');

        return $cleaningJob->property;
    }
}
