<?php

namespace Tests\Unit\Actions\Customers;

use App\Actions\Customers\UpdateCustomerAction;
use App\DTOs\Customer\CustomerData;
use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Models\User;
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
            'status' => CustomerStatus::Active,
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
            'status' => CustomerStatus::Active,
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

    public function test_it_soft_deletes_the_user_when_status_changes_to_cancelled(): void
    {
        $customer = Customer::factory()->withPortalAccess()->create([
            'status' => CustomerStatus::Active,
        ]);
        $userId = $customer->user_id;

        $this->action->execute(
            $this->makeCustomerData(status: CustomerStatus::Cancelled),
            $customer,
        );

        $this->assertSoftDeleted('users', ['id' => $userId]);
        $this->assertDatabaseHas('customers', [
            'id'     => $customer->id,
            'status' => CustomerStatus::Cancelled,
        ]);
    }

    public function test_it_does_not_delete_the_user_when_already_cancelled(): void
    {
        $customer = Customer::factory()->withPortalAccess()->cancelled()->create();
        $userId = $customer->user_id;

        $this->action->execute(
            $this->makeCustomerData(status: CustomerStatus::Cancelled),
            $customer,
        );

        $this->assertDatabaseHas('users', [
            'id'         => $userId,
            'deleted_at' => null,
        ]);
    }

    public function test_it_does_not_throw_when_cancelling_a_customer_with_no_portal_user(): void
    {
        $customer = Customer::factory()->create([
            'status'  => CustomerStatus::Active,
            'user_id' => null,
        ]);

        $this->action->execute(
            $this->makeCustomerData(status: CustomerStatus::Cancelled),
            $customer,
        );

        $this->assertDatabaseHas('customers', [
            'id'     => $customer->id,
            'status' => CustomerStatus::Cancelled,
        ]);
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
