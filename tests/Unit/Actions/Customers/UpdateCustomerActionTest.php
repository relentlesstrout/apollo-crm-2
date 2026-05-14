<?php

namespace Tests\Unit\Actions\Customers;

use App\Actions\Customers\UpdateCustomerAction;
use App\DTOs\Customer\CustomerData;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCustomerActionTest extends TestCase
{
    use RefreshDatabase;

    private UpdateCustomerAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new UpdateCustomerAction();
    }

    public function test_it_updates_a_customer_without_a_linked_user(): void
    {
        $customer = Customer::factory()->create([
            'name'   => 'Old Name',
            'phone'  => '0000000000',
            'email'  => 'old@example.com',
        ]);

        $this->action->execute($this->makeCustomerData(
            name:  'New Name',
            phone: '1111111111',
            email: 'new@example.com',
        ), $customer);

        $this->assertDatabaseHas('customers', [
            'id'     => $customer->id,
            'name'   => 'New Name',
            'phone'  => '1111111111',
            'email'  => 'new@example.com',
        ]);
    }

    public function test_it_syncs_the_linked_user_when_customer_has_portal_access(): void
    {
        $customer = Customer::factory()->withPortalAccess()->create([
            'name'  => 'Old Name',
            'phone' => '0000000000',
            'email' => 'old@example.com',
        ]);

        $this->action->execute($this->makeCustomerData(
            name:  'New Name',
            phone: '1111111111',
            email: 'new@example.com',
        ), $customer);

        $this->assertDatabaseHas('users', [
            'id'    => $customer->user_id,
            'name'  => 'New Name',
            'phone' => '1111111111',
            'email' => 'new@example.com',
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeCustomerData(
        string $name = 'Test Customer',
        string $phone = '0786412454',
        ?string $email = 'customer@example.com',
        bool $inviteToPortal = false,
    ): CustomerData {
        return new CustomerData(
            name:           $name,
            phone:          $phone,
            email:          $email,
            inviteToPortal: $inviteToPortal,
        );
    }
}
