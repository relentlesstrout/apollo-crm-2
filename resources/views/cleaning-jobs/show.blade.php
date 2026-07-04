<x-layouts.app>
    <div class="max-w-5xl mx-auto py-10 px-4">

        @php
            $statusColours = [
                'scheduled'   => 'bg-sky-100 text-sky-700 border border-sky-200',
                'in_progress' => 'bg-amber-100 text-amber-700 border border-amber-200',
                'completed'   => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                'cancelled'   => 'bg-red-100 text-red-700 border border-red-200',
            ];
            $statusBadge = $statusColours[$cleaningJob->status->value];
            $expectedTotal = $cleaningJob->services->sum(fn ($service) => $service->pivot->price);
        @endphp

        <div class="mb-6">
            <a href="{{ route('properties.show', $cleaningJob->property) }}"
               class="text-sm text-slate-500 hover:text-slate-700 transition-colors duration-150 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to {{ $cleaningJob->property->house }}, {{ $cleaningJob->property->street }}
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

            {{-- Job card --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-md border border-slate-200 overflow-hidden">
                    <div class="bg-slate-50 border-b border-slate-200 px-6 py-5 flex items-center justify-between">
                        <div>
                            <h1 class="text-lg font-semibold text-slate-800">Cleaning Job</h1>
                            <p class="text-sm text-slate-500">{{ $cleaningJob->property->house }}, {{ $cleaningJob->property->street }}, {{ $cleaningJob->property->postcode }}</p>
                        </div>
                        <span class="text-xs font-medium px-3 py-1 rounded-full {{ $statusBadge }}">
                            {{ $cleaningJob->status->label() }}
                        </span>
                    </div>

                    <dl class="px-6 py-4 grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <dt class="text-slate-500">Scheduled</dt>
                            <dd class="text-slate-800 font-medium">{{ $cleaningJob->scheduled_at->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Started</dt>
                            <dd class="text-slate-800 font-medium">{{ $cleaningJob->started_at?->format('d M Y, H:i') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Completed</dt>
                            <dd class="text-slate-800 font-medium">{{ $cleaningJob->completed_at?->format('d M Y, H:i') ?? '—' }}</dd>
                        </div>
                    </dl>

                    <div class="flex flex-wrap items-center gap-2 px-6 py-4 border-t border-slate-100">
                        @if (in_array($cleaningJob->status, [\App\Enums\CleaningJobStatus::Scheduled, \App\Enums\CleaningJobStatus::InProgress]))
                            <a href="{{ route('cleaning-jobs.edit', $cleaningJob) }}"
                               class="bg-white hover:bg-slate-100 text-slate-700 text-sm font-medium px-4 py-2 rounded-md border border-slate-200 transition-colors duration-150">
                                Edit
                            </a>
                        @endif

                        @if ($cleaningJob->status === \App\Enums\CleaningJobStatus::Scheduled)
                            <form method="POST" action="{{ route('cleaning-jobs.status', $cleaningJob) }}">
                                @csrf
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit"
                                        class="bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-medium px-4 py-2 rounded-md border border-amber-200 transition-colors duration-150">
                                    Start
                                </button>
                            </form>
                        @elseif ($cleaningJob->status === \App\Enums\CleaningJobStatus::InProgress)
                            <form method="POST" action="{{ route('cleaning-jobs.status', $cleaningJob) }}"
                                  onsubmit="return confirm('Mark this job complete?')">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                <button type="submit"
                                        class="bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium px-4 py-2 rounded-md border border-green-200 transition-colors duration-150">
                                    Complete
                                </button>
                            </form>
                        @endif

                        @if (in_array($cleaningJob->status, [\App\Enums\CleaningJobStatus::Scheduled, \App\Enums\CleaningJobStatus::InProgress]))
                            <form method="POST" action="{{ route('cleaning-jobs.status', $cleaningJob) }}"
                                  onsubmit="return confirm('Cancel this job?')">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit"
                                        class="bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium px-4 py-2 rounded-md border border-red-200 transition-colors duration-150">
                                    Cancel
                                </button>
                            </form>
                        @elseif ($cleaningJob->status === \App\Enums\CleaningJobStatus::Cancelled)
                            <form method="POST" action="{{ route('cleaning-jobs.status', $cleaningJob) }}"
                                  onsubmit="return confirm('Reschedule this job?')">
                                @csrf
                                <input type="hidden" name="status" value="scheduled">
                                <button type="submit"
                                        class="bg-sky-50 hover:bg-sky-100 text-sky-700 text-sm font-medium px-4 py-2 rounded-md border border-sky-200 transition-colors duration-150">
                                    Reschedule
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Customer context card --}}
            <div>
                <div class="bg-white rounded-md border border-slate-200 overflow-hidden">
                    <div class="bg-slate-50 border-b border-slate-200 px-6 py-5">
                        <h2 class="text-sm font-semibold text-slate-700">Customer</h2>
                    </div>
                    <div class="px-6 py-4 text-sm text-slate-700 leading-6">
                        <a href="{{ route('customers.show', $cleaningJob->property->customer) }}"
                           class="font-medium text-sky-600 hover:text-sky-800">
                            {{ $cleaningJob->property->customer->name }}
                        </a>
                        @if ($cleaningJob->property->customer->phone)
                            <p>{{ $cleaningJob->property->customer->phone }}</p>
                        @endif
                        @if ($cleaningJob->property->customer->email)
                            <p>{{ $cleaningJob->property->customer->email }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Services card --}}
        <div class="bg-white rounded-md border border-slate-200 overflow-hidden mb-4">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                <h2 class="text-sm font-semibold text-slate-700">Services</h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">
                        <th class="px-6 py-3">Service</th>
                        <th class="px-6 py-3 text-right">Expected</th>
                        <th class="px-6 py-3 text-right">Actual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($cleaningJob->services as $service)
                        <tr>
                            <td class="px-6 py-3 font-medium text-slate-800">{{ $service->name }}</td>
                            <td class="px-6 py-3 text-right text-slate-700">£{{ number_format($service->pivot->price / 100, 2) }}</td>
                            <td class="px-6 py-3 text-right text-slate-700">
                                {{ $service->pivot->actual_price !== null ? '£' . number_format($service->pivot->actual_price / 100, 2) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-slate-200 bg-slate-50">
                        <td class="px-6 py-3 font-semibold text-slate-700">Total</td>
                        <td class="px-6 py-3 text-right font-semibold text-slate-800">£{{ number_format($expectedTotal / 100, 2) }}</td>
                        <td class="px-6 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Cleaners card --}}
        <div class="bg-white rounded-md border border-slate-200 overflow-hidden mb-4">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                <h2 class="text-sm font-semibold text-slate-700">Assigned Cleaners</h2>
            </div>
            @if ($cleaningJob->cleaners->isEmpty())
                <p class="px-6 py-4 text-sm text-slate-500">No cleaners assigned.</p>
            @else
                <ul class="px-6 py-4 space-y-1 text-sm text-slate-700">
                    @foreach ($cleaningJob->cleaners as $cleaner)
                        <li>{{ $cleaner->name }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Notes card --}}
        @if ($cleaningJob->notes)
            <div class="bg-white rounded-md border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                    <h2 class="text-sm font-semibold text-slate-700">Notes</h2>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $cleaningJob->notes }}</p>
                </div>
            </div>
        @endif

    </div>
</x-layouts.app>
