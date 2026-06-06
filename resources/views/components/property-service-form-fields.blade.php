@props(['propertyService' => null, 'services'])

<div class="px-6 py-4">
    <label for="service_id" class="block text-sm font-medium text-slate-500 mb-1">Service</label>
    <select
        id="service_id"
        name="service_id"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('service_id') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    >
        <option value="">Select a service...</option>
        @foreach ($services as $service)
            <option value="{{ $service->id }}" {{ old('service_id', $propertyService?->service_id) == $service->id ? 'selected' : '' }}>
                {{ $service->name }}
            </option>
        @endforeach
    </select>
    @error('service_id')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="px-6 py-4">
    <label for="price" class="block text-sm font-medium text-slate-500 mb-1">Price (£)</label>
    <input
        type="number"
        id="price"
        name="price"
        step="0.01"
        min="0.01"
        value="{{ old('price', $propertyService ? number_format($propertyService->price / 100, 2) : '') }}"
        placeholder="15.00"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('price') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    />
    @error('price')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="px-6 py-4">
    <label for="description" class="block text-sm font-medium text-slate-500 mb-1">
        Description
        <span class="text-slate-400 font-normal">(optional — overrides the service default)</span>
    </label>
    <textarea
        id="description"
        name="description"
        rows="3"
        placeholder="Any property-specific instructions for this service..."
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('description') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    >{{ old('description', $propertyService?->description) }}</textarea>
    @error('description')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="px-6 py-4">
    <label for="effective_from" class="block text-sm font-medium text-slate-500 mb-1">Effective From</label>
    <input
        type="date"
        id="effective_from"
        name="effective_from"
        value="{{ old('effective_from', $propertyService?->effective_from?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('effective_from') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    />
    @error('effective_from')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="px-6 py-4">
    <label for="effective_to" class="block text-sm font-medium text-slate-500 mb-1">
        Effective To
        <span class="text-slate-400 font-normal">(optional — leave blank if ongoing)</span>
    </label>
    <input
        type="date"
        id="effective_to"
        name="effective_to"
        value="{{ old('effective_to', $propertyService?->effective_to?->format('Y-m-d')) }}"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('effective_to') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    />
    @error('effective_to')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
