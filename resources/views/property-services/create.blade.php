<x-layouts.app>
    <div class="max-w-2xl mx-auto py-10 px-4">

        <div class="mb-6">
            <a href="{{ route('properties.show', $property) }}"
               class="text-sm text-slate-500 hover:text-slate-700 transition-colors duration-150 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to {{ $property->house }}, {{ $property->street }}
            </a>
        </div>

        <div class="bg-white rounded-md border border-slate-200 overflow-hidden mb-4">

            <div class="bg-slate-50 border-b border-slate-200 px-6 py-5">
                <h1 class="text-lg font-semibold text-slate-800">Add Service</h1>
                <p class="text-sm text-slate-500">{{ $property->house }}, {{ $property->street }}, {{ $property->postcode }}</p>
            </div>

            <form method="POST" action="{{ route('properties.property-services.store', $property) }}">
                @csrf

                <div class="divide-y divide-slate-100">
                    <x-property-service-form-fields :services="$services" />
                </div>

                <div class="flex items-center justify-end gap-2 px-6 py-4 bg-slate-50 border-t border-slate-200">
                    <button
                        type="submit"
                        class="bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors duration-150"
                    >
                        Add Service
                    </button>
                    <a href="{{ route('properties.show', $property) }}"
                       class="bg-white hover:bg-slate-100 text-slate-700 text-sm font-medium px-4 py-2 rounded-md border border-slate-200 transition-colors duration-150">
                        Cancel
                    </a>
                </div>

            </form>
        </div>

    </div>
</x-layouts.app>
