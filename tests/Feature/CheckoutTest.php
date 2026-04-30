<?php

namespace Tests\Feature;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Mockery;
use Stripe\Service\InvoiceService;
use Stripe\Service\PaymentIntentService;
use Stripe\StripeClient;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_page_requires_valid_signature(): void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->get(route('checkout.show', $invoice));

        $response->assertForbidden();
    }

    public function test_checkout_page_returns_410_for_paid_invoice(): void
    {
        $invoice = Invoice::factory()->paid()->create();

        $url = URL::temporarySignedRoute('checkout.show', now()->addHour(), ['invoice' => $invoice->id]);

        $response = $this->get($url);

        $response->assertStatus(410);
    }

    public function test_checkout_page_renders_with_valid_signature(): void
    {
        $invoice = Invoice::factory()->create([
            'stripe_invoice_id' => 'in_test_checkout',
        ]);

        $this->mockStripeClientForCheckout();

        $url = URL::temporarySignedRoute('checkout.show', now()->addHour(), ['invoice' => $invoice->id]);

        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertSee($invoice->description);
        $response->assertSee($invoice->formattedAmount());
    }

    public function test_success_page_requires_valid_signature(): void
    {
        $invoice = Invoice::factory()->paid()->create();

        $response = $this->get(route('checkout.success', $invoice));

        $response->assertForbidden();
    }

    public function test_success_page_renders_with_valid_signature(): void
    {
        $invoice = Invoice::factory()->paid()->create();

        $url = URL::temporarySignedRoute('checkout.success', now()->addHour(), ['invoice' => $invoice->id]);

        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertSee('Payment Successful');
        $response->assertSee($invoice->description);
    }

    private function mockStripeClientForCheckout(): void
    {
        $invoiceService = Mockery::mock(InvoiceService::class);
        $invoiceService->shouldReceive('retrieve')->andReturn((object) [
            'id' => 'in_test_checkout',
            'payment_intent' => null,
        ]);

        $paymentIntentService = Mockery::mock(PaymentIntentService::class);
        $paymentIntentService->shouldReceive('create')->andReturn((object) [
            'id' => 'pi_test_new',
            'client_secret' => 'pi_test_new_secret_abc',
        ]);

        $stripeClient = Mockery::mock(StripeClient::class);
        $stripeClient->invoices = $invoiceService;
        $stripeClient->paymentIntents = $paymentIntentService;

        $this->app->instance(StripeClient::class, $stripeClient);
    }
}
