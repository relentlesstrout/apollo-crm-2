@props(['cleaningJob' => null, 'propertyServices', 'cleaners'])

@php
    $selectedServiceIds = $cleaningJob ? $cleaningJob->services->pluck('id')->all() : [];
    $jobPrices = $cleaningJob ? $cleaningJob->services->mapWithKeys(fn ($service) => [$service->id => $service->pivot->price])->all() : [];
    $selectedCleanerIds = $cleaningJob ? $cleaningJob->cleaners->pluck('id')->all() : [];
    $oldSelectedServices = old('services', $selectedServiceIds);
    $oldSelectedCleaners = old('cleaners', $selectedCleanerIds);
@endphp

<div class="px-6 py-4">
    <label for="scheduled_at" class="block text-sm font-medium text-slate-500 mb-1">Scheduled For</label>
    <input
        type="date"
        id="scheduled_at"
        name="scheduled_at"
        value="{{ old('scheduled_at', $cleaningJob?->scheduled_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('scheduled_at') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    />
    @error('scheduled_at')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="px-6 py-4">
    <span class="block text-sm font-medium text-slate-500 mb-2">Services</span>
    @if ($propertyServices->isEmpty())
        <p class="text-sm text-slate-500">This property has no services. Add a service before creating a job.</p>
    @else
        <div class="space-y-2">
            @foreach ($propertyServices as $propertyService)
                @php
                    $serviceId = $propertyService->service_id;
                    $isChecked = in_array($serviceId, $oldSelectedServices);
                    $priceValue = old("prices.{$serviceId}", number_format(($jobPrices[$serviceId] ?? $propertyService->price) / 100, 2, '.', ''));
                @endphp
                <div x-data="{ checked: {{ $isChecked ? 'true' : 'false' }} }"
                     class="flex items-center gap-3 rounded-md border border-slate-200 px-3 py-2">
                    <input
                        type="checkbox"
                        name="services[]"
                        value="{{ $serviceId }}"
                        x-model="checked"
                        class="w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500"
                    />
                    <span class="flex-1 text-sm font-medium text-slate-700">{{ $propertyService->service->name }}</span>
                    <div class="flex items-center gap-1">
                        <span class="text-sm text-slate-400">£</span>
                        <input
                            type="number"
                            step="0.01"
                            min="0.01"
                            name="prices[{{ $serviceId }}]"
                            value="{{ $priceValue }}"
                            x-bind:disabled="!checked"
                            class="w-24 rounded-md border border-slate-200 px-2 py-1 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 disabled:bg-slate-50 disabled:text-slate-400 @error("prices.{$serviceId}") border-red-300 @enderror"
                        />
                    </div>
                </div>
                @error("prices.{$serviceId}")
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            @endforeach
        </div>
        @error('services')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    @endif
</div>

<div class="px-6 py-4">
    <span class="block text-sm font-medium text-slate-500 mb-2">Assigned Cleaners</span>
    @if ($cleaners->isEmpty())
        <p class="text-sm text-slate-500">No cleaners available.</p>
    @else
        <div class="space-y-2">
            @foreach ($cleaners as $cleaner)
                <label class="flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        name="cleaners[]"
                        value="{{ $cleaner->id }}"
                        {{ in_array($cleaner->id, $oldSelectedCleaners) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500"
                    />
                    <span class="text-sm text-slate-700">{{ $cleaner->name }}</span>
                </label>
            @endforeach
        </div>
    @endif
</div>

<div class="px-6 py-4">
    <label for="notes" class="block text-sm font-medium text-slate-500 mb-1">Notes</label>
    <textarea
        id="notes"
        name="notes"
        rows="3"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('notes') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    >{{ old('notes', $cleaningJob?->notes) }}</textarea>
    @error('notes')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
