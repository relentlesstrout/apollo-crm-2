<x-layouts.app>
    <div class="max-w-5xl mx-auto py-10 px-4">

        <div class="mb-6">
            <a href="{{ route('customers.show', $property->customer) }}"
               class="text-sm text-slate-500 hover:text-slate-700 transition-colors duration-150 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to {{ $property->customer->name }}
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

            {{-- Address card --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-md border border-slate-200 overflow-hidden">

                    @php
                        $statusColours = [
                            'active'    => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                            'paused'    => 'bg-amber-100 text-amber-700 border border-amber-200',
                            'cancelled' => 'bg-red-100 text-red-700 border border-red-200',
                        ];
                        $statusBadge = $statusColours[$property->status->value] ?? $statusColours['active'];
                    @endphp

                    <div class="bg-slate-50 border-b border-slate-200 px-6 py-5 flex items-center justify-between">
                        <div>
                            <h1 class="text-lg font-semibold text-slate-800">
                                {{ $property->house }}, {{ $property->street }}
                            </h1>
                            <p class="text-sm text-slate-500">{{ $property->postcode }}</p>
                        </div>
                        <span class="text-xs font-medium px-3 py-1 rounded-full {{ $statusBadge }}">
                            {{ $property->status->label() }}
                        </span>
                    </div>

                    <div class="px-6 py-4">
                        <address class="not-italic text-sm text-slate-700 leading-6">
                            <p>{{ $property->house }} {{ $property->street }}</p>
                            @if ($property->area)
                                <p>{{ $property->area }}</p>
                            @endif
                            <p>{{ $property->postcode }}</p>
                        </address>
                    </div>

                    <div class="flex items-center gap-2 px-6 py-4 border-t border-slate-100">
                        <a href="{{ route('properties.edit', $property) }}"
                           class="bg-white hover:bg-slate-100 text-slate-700 text-sm font-medium px-4 py-2 rounded-md border border-slate-200 transition-colors duration-150">
                            Edit
                        </a>

                        @if ($property->status === \App\Enums\PropertyStatus::Active)
                            <form method="POST" action="{{ route('properties.status', $property) }}"
                                  onsubmit="return confirm('Pause this property?')">
                                @csrf
                                <input type="hidden" name="status" value="paused">
                                <button type="submit"
                                        class="bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-medium px-4 py-2 rounded-md border border-amber-200 transition-colors duration-150">
                                    Pause
                                </button>
                            </form>
                            <form method="POST" action="{{ route('properties.status', $property) }}"
                                  onsubmit="return confirm('Cancel this property?')">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit"
                                        class="bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium px-4 py-2 rounded-md border border-red-200 transition-colors duration-150">
                                    Cancel
                                </button>
                            </form>
                        @elseif ($property->status === \App\Enums\PropertyStatus::Paused)
                            <form method="POST" action="{{ route('properties.status', $property) }}"
                                  onsubmit="return confirm('Resume this property?')">
                                @csrf
                                <input type="hidden" name="status" value="active">
                                <button type="submit"
                                        class="bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium px-4 py-2 rounded-md border border-green-200 transition-colors duration-150">
                                    Resume
                                </button>
                            </form>
                            <form method="POST" action="{{ route('properties.status', $property) }}"
                                  onsubmit="return confirm('Cancel this property?')">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit"
                                        class="bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium px-4 py-2 rounded-md border border-red-200 transition-colors duration-150">
                                    Cancel
                                </button>
                            </form>
                        @elseif ($property->status === \App\Enums\PropertyStatus::Cancelled)
                            <form method="POST" action="{{ route('properties.status', $property) }}"
                                  onsubmit="return confirm('Reactivate this property?')">
                                @csrf
                                <input type="hidden" name="status" value="active">
                                <button type="submit"
                                        class="bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium px-4 py-2 rounded-md border border-green-200 transition-colors duration-150">
                                    Reactivate
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Customer context card --}}
            <div>
                <div class="bg-white rounded-md border border-slate-200 overflow-hidden">
                    <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                        <h2 class="text-sm font-semibold text-slate-700">Customer</h2>
                    </div>

                    @php
                        $customer = $property->customer;
                        $customerStatusColours = [
                            'active'    => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                            'paused'    => 'bg-amber-100 text-amber-700 border border-amber-200',
                            'cancelled' => 'bg-red-100 text-red-700 border border-red-200',
                        ];
                        $customerBadge = $customerStatusColours[$customer->status->value] ?? $customerStatusColours['active'];
                    @endphp

                    <dl class="divide-y divide-slate-100">
                        <div class="px-6 py-3 flex items-start justify-between gap-4">
                            <dt class="text-sm font-medium text-slate-500 shrink-0">Name</dt>
                            <dd class="text-sm text-right">
                                <a href="{{ route('customers.show', $customer) }}"
                                   class="text-sky-600 hover:text-sky-800 font-medium">
                                    {{ $customer->name }}
                                </a>
                            </dd>
                        </div>
                        <div class="px-6 py-3 flex items-start justify-between gap-4">
                            <dt class="text-sm font-medium text-slate-500 shrink-0">Phone</dt>
                            <dd class="text-sm text-slate-800 text-right">{{ $customer->phone }}</dd>
                        </div>
                        <div class="px-6 py-3 flex items-start justify-between gap-4">
                            <dt class="text-sm font-medium text-slate-500 shrink-0">Email</dt>
                            <dd class="text-sm text-slate-800 text-right">{{ $customer->email ?? '—' }}</dd>
                        </div>
                        <div class="px-6 py-3 flex items-start justify-between gap-4">
                            <dt class="text-sm font-medium text-slate-500 shrink-0">Status</dt>
                            <dd class="text-sm text-right">
                                <span class="text-xs font-medium px-3 py-1 rounded-full {{ $customerBadge }}">
                                    {{ $customer->status->label() }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Notes card --}}
        @if ($property->notes)
            <div class="bg-white rounded-md border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                    <h2 class="text-sm font-semibold text-slate-700">Notes</h2>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $property->notes }}</p>
                </div>
            </div>
        @endif

    </div>
</x-layouts.app>
