<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function __construct(private StripeClient $stripe) {}

    public function show(Invoice $invoice): View
    {
        abort_if($invoice->status === InvoiceStatus::Paid, 410, 'This invoice has already been paid.');

        $invoice->loadMissing('user');

        $stripeInvoice = $this->stripe->invoices->retrieve($invoice->stripe_invoice_id);

        // For send_invoice invoices, Stripe doesn't create a PaymentIntent
        // until payment is attempted. Create a standalone PaymentIntent
        // linked to this customer for our custom checkout.
        if ($stripeInvoice->payment_intent) {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($stripeInvoice->payment_intent);
        } else {
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $invoice->amount,
                'currency' => $invoice->currency,
                'customer' => $invoice->user->stripe_id,
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'stripe_invoice_id' => $invoice->stripe_invoice_id,
                ],
            ]);

            $invoice->update(['stripe_payment_intent_id' => $paymentIntent->id]);
        }

        $successUrl = url()->temporarySignedRoute(
            'checkout.success',
            now()->addHour(),
            ['invoice' => $invoice->id]
        );

        return view('checkout.show', [
            'invoice' => $invoice,
            'stripeKey' => config('cashier.key'),
            'clientSecret' => $paymentIntent->client_secret,
            'successUrl' => $successUrl,
        ]);
    }

    public function success(Request $request, Invoice $invoice): View
    {
        abort_unless(
            $request->hasValidSignatureWhileIgnoring(['payment_intent', 'payment_intent_client_secret', 'redirect_status']),
            403,
            'Invalid signature.'
        );

        if ($invoice->status !== InvoiceStatus::Paid) {
            if ($invoice->stripe_payment_intent_id) {
                $paymentIntent = $this->stripe->paymentIntents->retrieve($invoice->stripe_payment_intent_id);

                if ($paymentIntent->status === 'succeeded') {
                    // Pay the Stripe invoice out of band since we collected payment ourselves
                    $this->stripe->invoices->pay($invoice->stripe_invoice_id, [
                        'paid_out_of_band' => true,
                    ]);

                    $invoice->update([
                        'status' => InvoiceStatus::Paid,
                        'paid_at' => now(),
                    ]);
                }
            }
        }

        return view('checkout.success', [
            'invoice' => $invoice->fresh(),
        ]);
    }
}
