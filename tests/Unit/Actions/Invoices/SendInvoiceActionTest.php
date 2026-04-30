<?php

namespace Tests\Unit\Actions\Invoices;

use App\Actions\Invoices\SendInvoiceAction;
use App\DTOs\Invoice\SendInvoiceData;
use App\Enums\InvoiceStatus;
use App\Enums\UserRole;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Stripe\Service\InvoiceItemService;
use Stripe\Service\InvoiceService;
use Stripe\StripeClient;
use Tests\TestCase;

class SendInvoiceActionTest extends TestCase
{
    use RefreshDatabase;

    private StripeClient $stripeClient;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        $this->mockStripeClient();
    }

    public function test_it_creates_local_invoice_record(): void
    {
        $user = $this->createCustomer();

        $action = app(SendInvoiceAction::class);
        $invoice = $action->execute(new SendInvoiceData(userId: $user->id));

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals($user->id, $invoice->user_id);
        $this->assertEquals('Window Cleaning Service', $invoice->description);
        $this->assertEquals(5000, $invoice->amount);
        $this->assertEquals('gbp', $invoice->currency);
        $this->assertEquals(InvoiceStatus::Sent, $invoice->status);
        $this->assertEquals('in_mock_123', $invoice->stripe_invoice_id);
    }

    public function test_it_sends_invoice_email_to_customer(): void
    {
        $user = $this->createCustomer();

        $action = app(SendInvoiceAction::class);
        $action->execute(new SendInvoiceData(userId: $user->id));

        Mail::assertSent(InvoiceMail::class, function (InvoiceMail $mail) use ($user): bool {
            return $mail->hasTo($user->email);
        });
    }

    public function test_it_sends_exactly_one_email(): void
    {
        $user = $this->createCustomer();

        $action = app(SendInvoiceAction::class);
        $action->execute(new SendInvoiceData(userId: $user->id));

        Mail::assertSentCount(1);
    }

    public function test_invoice_is_persisted_to_database(): void
    {
        $user = $this->createCustomer();

        $action = app(SendInvoiceAction::class);
        $action->execute(new SendInvoiceData(userId: $user->id));

        $this->assertDatabaseHas('invoices', [
            'user_id' => $user->id,
            'stripe_invoice_id' => 'in_mock_123',
            'description' => 'Window Cleaning Service',
            'amount' => 5000,
            'currency' => 'gbp',
            'status' => InvoiceStatus::Sent->value,
        ]);
    }

    private function createCustomer(): User
    {
        return User::factory()->create([
            'role' => UserRole::Customer,
            'stripe_id' => 'cus_test123',
        ]);
    }

    private function mockStripeClient(): void
    {
        $mockInvoice = (object) [
            'id' => 'in_mock_123',
            'payment_intent' => 'pi_mock_123',
        ];

        $invoiceService = Mockery::mock(InvoiceService::class);
        $invoiceService->shouldReceive('create')->andReturn($mockInvoice);
        $invoiceService->shouldReceive('finalizeInvoice')->andReturn($mockInvoice);

        $invoiceItemService = Mockery::mock(InvoiceItemService::class);
        $invoiceItemService->shouldReceive('create')->andReturn((object) ['id' => 'ii_mock_123']);

        $stripeClient = Mockery::mock(StripeClient::class);
        $stripeClient->invoices = $invoiceService;
        $stripeClient->invoiceItems = $invoiceItemService;

        $this->app->instance(StripeClient::class, $stripeClient);
    }
}
