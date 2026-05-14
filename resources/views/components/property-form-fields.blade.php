@props(['property' => null])

<div class="px-6 py-4">
    <label for="house" class="block text-sm font-medium text-slate-500 mb-1">House / Building</label>
    <input
        type="text"
        id="house"
        name="house"
        value="{{ old('house', $property?->house) }}"
        placeholder="12"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('house') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    />
    @error('house')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="px-6 py-4">
    <label for="street" class="block text-sm font-medium text-slate-500 mb-1">Street</label>
    <input
        type="text"
        id="street"
        name="street"
        value="{{ old('street', $property?->street) }}"
        placeholder="High Street"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('street') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    />
    @error('street')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="px-6 py-4">
    <label for="area" class="block text-sm font-medium text-slate-500 mb-1">
        Area
        <span class="text-slate-400 font-normal">(optional)</span>
    </label>
    <input
        type="text"
        id="area"
        name="area"
        value="{{ old('area', $property?->area) }}"
        placeholder="Mayfair"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('area') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    />
    @error('area')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="px-6 py-4">
    <label for="postcode" class="block text-sm font-medium text-slate-500 mb-1">Postcode</label>
    <input
        type="text"
        id="postcode"
        name="postcode"
        value="{{ old('postcode', $property?->postcode) }}"
        placeholder="SW1A 1AA"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('postcode') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    />
    @error('postcode')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="px-6 py-4">
    <label for="notes" class="block text-sm font-medium text-slate-500 mb-1">
        Notes
        <span class="text-slate-400 font-normal">(optional)</span>
    </label>
    <textarea
        id="notes"
        name="notes"
        rows="3"
        placeholder="Any relevant details about the property..."
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('notes') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    >{{ old('notes', $property?->notes) }}</textarea>
    @error('notes')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
