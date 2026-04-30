<?php

namespace App\Actions\Invoices;

use App\DTOs\Invoice\SendInvoiceData;
use App\Enums\InvoiceStatus;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Stripe\StripeClient;

class SendInvoiceAction
{
    public function __construct(private StripeClient $stripe) {}

    public function execute(SendInvoiceData $data): Invoice
    {
        $user = User::findOrFail($data->userId);

        if (! $user->hasStripeId()) {
            $user->createAsStripeCustomer([
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }

        $stripeInvoice = $this->stripe->invoices->create([
            'customer' => $user->stripe_id,
            'collection_method' => 'send_invoice',
            'days_until_due' => 30,
            'currency' => $data->currency,
        ]);

        $this->stripe->invoiceItems->create([
            'customer' => $user->stripe_id,
            'invoice' => $stripeInvoice->id,
            'amount' => $data->amount,
            'currency' => $data->currency,
            'description' => $data->description,
        ]);

        $stripeInvoice = $this->stripe->invoices->finalizeInvoice($stripeInvoice->id);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'stripe_invoice_id' => $stripeInvoice->id,
            'stripe_payment_intent_id' => $stripeInvoice->payment_intent,
            'description' => $data->description,
            'amount' => $data->amount,
            'currency' => $data->currency,
            'status' => InvoiceStatus::Sent,
        ]);

        $checkoutUrl = URL::temporarySignedRoute(
            'checkout.show',
            now()->addDays(30),
            ['invoice' => $invoice->id]
        );

        Mail::to($user->email)->send(new InvoiceMail($invoice, $checkoutUrl));

        return $invoice;
    }
}
