@props(['service' => null])

<div class="px-6 py-4">
    <label for="name" class="block text-sm font-medium text-slate-500 mb-1">Name</label>
    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $service?->name) }}"
        placeholder="Window Clean"
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('name') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    />
    @error('name')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="px-6 py-4">
    <label for="description" class="block text-sm font-medium text-slate-500 mb-1">
        Description
        <span class="text-slate-400 font-normal">(optional)</span>
    </label>
    <textarea
        id="description"
        name="description"
        rows="4"
        placeholder="Describe what this service involves so cleaners know exactly what is required..."
        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('description') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
    >{{ old('description', $service?->description) }}</textarea>
    @error('description')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
