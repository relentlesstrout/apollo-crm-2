<x-layouts.guest>
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <x-logo class="mx-auto h-10 w-auto" />
            <h1 class="mt-4 text-2xl font-bold text-slate-800">Checkout</h1>
        </div>

        <div class="bg-white rounded-md border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-5">
                <h2 class="text-lg font-semibold text-slate-800">Invoice Details</h2>
                <p class="text-sm text-slate-500 mt-1">Please review and pay your invoice below.</p>
            </div>

            <div class="px-6 py-5">
                <dl class="divide-y divide-slate-100">
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-sm font-medium text-slate-500">Description</dt>
                        <dd class="text-sm text-slate-800">{{ $invoice->description }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-sm font-medium text-slate-500">Amount</dt>
                        <dd class="text-lg font-semibold text-slate-800">{{ $invoice->formattedAmount() }}</dd>
                    </div>
                </dl>
            </div>

            <div class="border-t border-slate-200 px-6 py-5">
                <form id="payment-form">
                    <div id="payment-element" class="mb-4"></div>
                    <div id="error-message" class="text-red-600 text-sm mb-4 hidden"></div>
                    <button id="submit-button"
                            type="submit"
                            class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium px-4 py-3 rounded-md transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        Pay {{ $invoice->formattedAmount() }}
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            Payments are securely processed by Stripe.
        </p>
    </div>

    <script>
        const stripe = Stripe('{{ $stripeKey }}');
        const elements = stripe.elements({
            clientSecret: '{{ $clientSecret }}',
            appearance: {
                theme: 'stripe',
                variables: {
                    colorPrimary: '#10b981',
                    borderRadius: '6px',
                },
            },
        });

        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const errorMessage = document.getElementById('error-message');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';
            errorMessage.classList.add('hidden');

            const { error } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: '{!! $successUrl !!}',
                },
            });

            if (error) {
                errorMessage.textContent = error.message;
                errorMessage.classList.remove('hidden');
                submitButton.disabled = false;
                submitButton.textContent = 'Pay {{ $invoice->formattedAmount() }}';
            }
        });
    </script>
</x-layouts.guest>
