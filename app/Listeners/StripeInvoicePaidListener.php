<?php

namespace App\Listeners;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Cashier\Events\WebhookReceived;

class StripeInvoicePaidListener implements ShouldQueue
{
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] !== 'invoice.payment_succeeded') {
            return;
        }

        $stripeInvoiceId = $event->payload['data']['object']['id'] ?? null;

        if (! $stripeInvoiceId) {
            return;
        }

        $invoice = Invoice::where('stripe_invoice_id', $stripeInvoiceId)->first();

        if (! $invoice || $invoice->status === InvoiceStatus::Paid) {
            return;
        }

        $invoice->update([
            'status' => InvoiceStatus::Paid,
            'paid_at' => now(),
            'stripe_payment_intent_id' => $event->payload['data']['object']['payment_intent'] ?? null,
        ]);
    }
}
