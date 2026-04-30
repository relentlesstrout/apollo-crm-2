<x-layouts.guest>
    <div class="w-full max-w-md text-center">
        <div class="mb-8">
            <x-logo class="mx-auto h-10 w-auto" />
        </div>

        <div class="bg-white rounded-md border border-slate-200 overflow-hidden px-6 py-10">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 mb-6">
                <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-slate-800 mb-2">Payment Successful</h1>
            <p class="text-slate-500 mb-6">Thank you for your payment.</p>

            <dl class="divide-y divide-slate-100 text-left">
                <div class="flex items-center justify-between py-3">
                    <dt class="text-sm font-medium text-slate-500">Description</dt>
                    <dd class="text-sm text-slate-800">{{ $invoice->description }}</dd>
                </div>
                <div class="flex items-center justify-between py-3">
                    <dt class="text-sm font-medium text-slate-500">Amount Paid</dt>
                    <dd class="text-sm font-semibold text-slate-800">{{ $invoice->formattedAmount() }}</dd>
                </div>
                @if($invoice->paid_at)
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-sm font-medium text-slate-500">Paid On</dt>
                        <dd class="text-sm text-slate-800">{{ $invoice->paid_at->format('j F Y') }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <p class="text-xs text-slate-400 mt-6">
            A confirmation has been sent to your email address.
        </p>
    </div>
</x-layouts.guest>
