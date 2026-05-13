<?php

namespace Tests\Unit\Actions\Customers;

use App\Actions\Customers\CreateCustomerAction;
use App\Actions\Customers\GrantCustomerPortalAccessAction;
use App\DTOs\Customer\CustomerData;
use App\Enums\CustomerStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CreateCustomerActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateCustomerAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->action = new CreateCustomerAction(new GrantCustomerPortalAccessAction());
    }

    public function test_it_creates_a_customer_from_customer_data(): void
    {
        $customer = $this->action->execute($this->makeCustomerData());

        $this->assertNotNull($customer->id);

        $this->assertDatabaseHas('customers', [
            'name'   => 'Test Customer',
            'phone'  => '0786412454',
            'email'  => 'customer@example.com',
            'status' => CustomerStatus::Active,
        ]);
    }

    public function test_it_does_not_grant_portal_access_by_default(): void
    {
        $customer = $this->action->execute($this->makeCustomerData());

        $this->assertNull($customer->user_id);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_it_grants_portal_access_when_invited_and_email_is_present(): void
    {
        $customer = $this->action->execute($this->makeCustomerData(
            email: 'customer@example.com',
            inviteToPortal: true,
        ));

        $this->assertNotNull($customer->fresh()->user_id);
        $this->assertDatabaseHas('users', [
            'email' => 'customer@example.com',
            'role'  => UserRole::Customer,
        ]);
    }

    public function test_it_does_not_grant_portal_access_when_email_is_missing(): void
    {
        $customer = $this->action->execute($this->makeCustomerData(
            email: null,
            inviteToPortal: true,
        ));

        $this->assertNull($customer->user_id);
        $this->assertDatabaseCount('users', 0);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeCustomerData(
        string $name = 'Test Customer',
        string $phone = '0786412454',
        ?string $email = 'customer@example.com',
        CustomerStatus $status = CustomerStatus::Active,
        bool $inviteToPortal = false,
    ): CustomerData {
        return new CustomerData(
            name:           $name,
            phone:          $phone,
            email:          $email,
            status:         $status,
            inviteToPortal: $inviteToPortal,
        );
    }
}
