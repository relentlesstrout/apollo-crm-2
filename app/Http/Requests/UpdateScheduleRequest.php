<?php

namespace App\Http\Requests;

use App\DTOs\Schedule\ScheduleData;
use App\Models\Schedule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
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
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'frequency_weeks' => ['required', 'integer', 'in:1,2,4,8,12,16'],
            'next_due_at' => ['required', 'date'],
            'active' => ['boolean'],
        ];
    }

    public function toDTO(Schedule $schedule): ScheduleData
    {
        return new ScheduleData(
            serviceId: $this->integer('service_id'),
            frequencyWeeks: $this->integer('frequency_weeks'),
            activeAt: $this->boolean('active')
                ? ($schedule->active_at?->toDateTimeString() ?? now()->toDateTimeString())
                : null,
            nextDueAt: $this->string('next_due_at')->toString(),
        );
    }
}
