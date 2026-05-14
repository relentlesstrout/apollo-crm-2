<x-layouts.app>
    <div class="max-w-3xl mx-auto py-10 px-4">

        <div class="mb-6">
            <a href="{{ route('customers.index') }}"
               class="text-sm text-slate-500 hover:text-slate-700 transition-colors duration-150 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Customers
            </a>
        </div>

        {{-- Customer details --}}
        <div class="bg-white rounded-md border border-slate-200 overflow-hidden mb-4">

            @php
                $statusColours = [
                    'active'    => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                    'paused'    => 'bg-amber-100 text-amber-700 border border-amber-200',
                    'cancelled' => 'bg-red-100 text-red-700 border border-red-200',
                ];
                $statusBadge = $statusColours[$customer->status->value] ?? $statusColours['active'];
            @endphp

            <div class="bg-slate-50 border-b border-slate-200 px-6 py-5 flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-slate-800">{{ $customer->name }}</h1>
                    <p class="text-sm text-slate-500">{{ $customer->email ?? 'No email on record' }}</p>
                </div>
                <span class="text-xs font-medium px-3 py-1 rounded-full {{ $statusBadge }}">
                    {{ $customer->status->label() }}
                </span>
            </div>

            <dl class="divide-y divide-slate-100">

                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <dt class="text-sm font-medium text-slate-500 w-32 shrink-0">Name</dt>
                    <dd class="text-sm text-slate-800 text-right">{{ $customer->name }}</dd>
                </div>

                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <dt class="text-sm font-medium text-slate-500 w-32 shrink-0">Phone</dt>
                    <dd class="text-sm text-slate-800 text-right">{{ $customer->phone }}</dd>
                </div>

                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <dt class="text-sm font-medium text-slate-500 w-32 shrink-0">Email</dt>
                    <dd class="text-sm text-slate-800 text-right">{{ $customer->email ?? '—' }}</dd>
                </div>

                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <dt class="text-sm font-medium text-slate-500 w-32 shrink-0">Status</dt>
                    <dd class="text-sm text-right">
                        <span class="text-xs font-medium px-3 py-1 rounded-full {{ $statusBadge }}">
                            {{ $customer->status->label() }}
                        </span>
                    </dd>
                </div>

                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <dt class="text-sm font-medium text-slate-500 w-32 shrink-0">Portal access</dt>
                    <dd class="text-sm text-slate-800 text-right">{{ $customer->hasPortalAccess() ? 'Yes' : 'No' }}</dd>
                </div>

                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <dt class="text-sm font-medium text-slate-500 w-32 shrink-0">Created</dt>
                    <dd class="text-sm text-slate-800 text-right">{{ $customer->created_at->format('j F Y') }}</dd>
                </div>

            </dl>
        </div>

        <div class="flex items-center justify-end gap-2 mb-8">
            <a href="{{ route('customers.edit', $customer) }}"
               class="bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors duration-150">
                Edit
            </a>
        </div>

        {{-- Properties --}}
        @php
            $activeProps    = $customer->properties->where('status', \App\Enums\PropertyStatus::Active)->values();
            $pausedProps    = $customer->properties->where('status', \App\Enums\PropertyStatus::Paused)->values();
            $cancelledProps = $customer->properties->where('status', \App\Enums\PropertyStatus::Cancelled)->values();
            $visibleProps   = $activeProps->merge($pausedProps);
        @endphp

        <div class="bg-white rounded-md border border-slate-200 overflow-hidden mb-4">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">Properties</h2>
                <a href="{{ route('customers.properties.create', $customer) }}"
                   class="bg-sky-500 hover:bg-sky-600 text-white text-xs font-medium px-3 py-1.5 rounded-md transition-colors duration-150">
                    Add Property
                </a>
            </div>

            @if ($visibleProps->isEmpty() && $cancelledProps->isEmpty())
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-slate-400">No properties yet.</p>
                </div>
            @else
                <div x-data="{ showCancelled: false }">

                    @if ($visibleProps->isEmpty())
                        <div class="px-6 py-4 text-center">
                            <p class="text-sm text-slate-400">No active or paused properties.</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach ($visibleProps as $property)
                                @php
                                    $propBadge = match ($property->status) {
                                        \App\Enums\PropertyStatus::Active => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                                        \App\Enums\PropertyStatus::Paused => 'bg-amber-100 text-amber-700 border border-amber-200',
                                        default                           => 'bg-red-100 text-red-700 border border-red-200',
                                    };
                                @endphp
                                <div class="px-6 py-4 flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-slate-800">{{ $property->house }}, {{ $property->street }}</p>
                                        <p class="text-xs text-slate-500">
                                            @if ($property->area){{ $property->area }}, @endif{{ $property->postcode }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="text-xs font-medium px-2.5 py-0.5 rounded-full {{ $propBadge }}">
                                            {{ $property->status->label() }}
                                        </span>
                                        <a href="{{ route('properties.show', $property) }}"
                                           class="bg-sky-500 hover:bg-sky-600 text-white text-xs font-medium px-3 py-1.5 rounded-md transition-colors duration-150">
                                            View
                                        </a>
                                        <a href="{{ route('properties.edit', $property) }}"
                                           class="bg-white hover:bg-slate-100 text-slate-700 text-xs font-medium px-3 py-1.5 rounded-md border border-slate-200 transition-colors duration-150">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($cancelledProps->isNotEmpty())
                        <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/50">
                            <button @click="showCancelled = !showCancelled"
                                    class="text-xs text-slate-500 hover:text-slate-700 flex items-center gap-1">
                                <svg class="w-3 h-3 transition-transform duration-150" :class="showCancelled ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                                <span x-text="showCancelled ? 'Hide cancelled' : 'Show {{ $cancelledProps->count() }} cancelled'"></span>
                            </button>
                        </div>

                        <div x-show="showCancelled" class="divide-y divide-slate-100">
                            @foreach ($cancelledProps as $property)
                                <div class="px-6 py-4 flex items-center justify-between gap-4 bg-slate-50/30">
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">{{ $property->house }}, {{ $property->street }}</p>
                                        <p class="text-xs text-slate-400">
                                            @if ($property->area){{ $property->area }}, @endif{{ $property->postcode }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 border border-red-200">
                                            Cancelled
                                        </span>
                                        <a href="{{ route('properties.show', $property) }}"
                                           class="bg-sky-500 hover:bg-sky-600 text-white text-xs font-medium px-3 py-1.5 rounded-md transition-colors duration-150">
                                            View
                                        </a>
                                        <a href="{{ route('properties.edit', $property) }}"
                                           class="bg-white hover:bg-slate-100 text-slate-700 text-xs font-medium px-3 py-1.5 rounded-md border border-slate-200 transition-colors duration-150">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            @endif
        </div>

        {{-- Invoices placeholder --}}
        <div class="bg-white rounded-md border border-slate-200 overflow-hidden mb-4">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                <h2 class="text-sm font-semibold text-slate-700">Invoices</h2>
            </div>
            <div class="px-6 py-8 text-center">
                <p class="text-sm text-slate-400">Invoices coming soon.</p>
            </div>
        </div>

        {{-- Quotes placeholder --}}
        <div class="bg-white rounded-md border border-slate-200 overflow-hidden mb-4">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                <h2 class="text-sm font-semibold text-slate-700">Quotes</h2>
            </div>
            <div class="px-6 py-8 text-center">
                <p class="text-sm text-slate-400">Quotes coming soon.</p>
            </div>
        </div>

    </div>
</x-layouts.app>
