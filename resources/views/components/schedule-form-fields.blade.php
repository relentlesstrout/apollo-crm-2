@props(['schedule' => null, 'services'])

<div class="px-6 py-4">
    <label for="service_id" class="block text-sm font-medium text-slate-500 mb-1">Service</label>
    <select
        id="service_id"
        name="service_id"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('service_id') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    >
        <option value="">Select a service...</option>
        @foreach ($services as $service)
            <option value="{{ $service->id }}" {{ old('service_id', $schedule?->service_id) == $service->id ? 'selected' : '' }}>
                {{ $service->name }}
            </option>
        @endforeach
    </select>
    @error('service_id')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="px-6 py-4">
    <label for="frequency_weeks" class="block text-sm font-medium text-slate-500 mb-1">Frequency</label>
    <select
        id="frequency_weeks"
        name="frequency_weeks"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('frequency_weeks') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    >
        @foreach ([1 => 'Every week', 2 => 'Every 2 weeks', 4 => 'Every 4 weeks', 8 => 'Every 8 weeks', 12 => 'Every 12 weeks', 16 => 'Every 16 weeks'] as $weeks => $label)
            <option value="{{ $weeks }}" {{ old('frequency_weeks', $schedule?->frequency_weeks ?? 4) == $weeks ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('frequency_weeks')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="px-6 py-4">
    <label for="next_due_at" class="block text-sm font-medium text-slate-500 mb-1">Next Due</label>
    <input
        type="date"
        id="next_due_at"
        name="next_due_at"
        value="{{ old('next_due_at', $schedule?->next_due_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('next_due_at') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    />
    @error('next_due_at')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

@if ($schedule)
    <div class="px-6 py-4">
        <label class="flex items-center gap-3 cursor-pointer">
            <input
                type="checkbox"
                name="active"
                value="1"
                {{ old('active', $schedule->active_at !== null) ? 'checked' : '' }}
                class="w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500"
            />
            <span class="text-sm font-medium text-slate-700">Active</span>
        </label>
        @if ($schedule->active_at)
            <p class="mt-1 text-xs text-slate-400">Active since {{ $schedule->active_at->format('d M Y') }}</p>
        @endif
    </div>
@endif
