<?php

namespace Tests\Unit\Actions\Customers;

use App\Actions\Customers\RecomputeCustomerStatusAction;
use App\Enums\CustomerStatus;
use App\Enums\PropertyStatus;
use App\Models\Customer;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecomputeCustomerStatusActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new RecomputeCustomerStatusAction;
    }

    public function test_it_updates_the_customer_status(): void
    {
        $customer = Customer::factory()->create();
        $property = Property::factory()->for($customer)->create();

        $property->update(['status' => PropertyStatus::Paused]);

        $this->action->execute($customer);

        $this->assertEquals(CustomerStatus::Paused, $customer->fresh()->status);
    }

    public function test_it_updates_the_customer_status_for_multiple_properties(): void
    {
        $customer = Customer::factory()->create();
        $activeProperty = Property::factory()->for($customer)->create();
        Property::factory()->for($customer)->paused()->create();

        $activeProperty->update(['status' => PropertyStatus::Paused]);

        $this->action->execute($customer);

        $this->assertEquals(CustomerStatus::Paused, $customer->fresh()->status);
    }

    public function test_it_does_not_pause_customer_when_one_property_is_still_active(): void
    {
        $customer = Customer::factory()->create();
        $property1 = Property::factory()->for($customer)->create();
        $property2 = Property::factory()->for($customer)->create();

        $property2->status = 'paused';

        $this->action->execute($customer);

        $this->assertEquals(CustomerStatus::Active, $customer->fresh()->status);
    }

    public function test_it_cancels_a_customer_when_all_properties_are_cancelled(): void
    {
        $customer = Customer::factory()->create();
        Property::factory()->for($customer)->cancelled()->count(3)->create();

        $this->action->execute($customer);

        $this->assertEquals(CustomerStatus::Cancelled, $customer->fresh()->status);
    }

    public function test_it_updates_the_customer_status_to_cancelled(): void
    {
        $customer = Customer::factory()->create();
        $property = Property::factory()->for($customer)->create();

        $property->update(['status' => PropertyStatus::Cancelled]);

        $this->action->execute($customer);

        $this->assertEquals(CustomerStatus::Cancelled, $customer->fresh()->status);
    }

    public function test_it_updates_the_customer_status_to_cancelled_for_multiple_properties(): void
    {
        $customer = Customer::factory()->create();
        $pausedProperty = Property::factory()->for($customer)->paused()->create();
        Property::factory()->for($customer)->cancelled()->create();

        $pausedProperty->update(['status' => PropertyStatus::Cancelled]);

        $this->action->execute($customer);

        $this->assertEquals(CustomerStatus::Cancelled, $customer->fresh()->status);
    }

    public function test_it_does_not_cancel_customer_when_one_property_is_still_active(): void
    {
        $customer = Customer::factory()->create();
        Property::factory()->for($customer)->create();
        $cancelledProperty = Property::factory()->for($customer)->create();

        $cancelledProperty->status = 'cancelled';

        $this->action->execute($customer);

        $this->assertEquals(CustomerStatus::Active, $customer->fresh()->status);
    }

    public function test_it_does_not_cancel_customer_when_one_property_is_still_paused(): void
    {
        $customer = Customer::factory()->create();
        Property::factory()->for($customer)->paused()->count(1)->create();
        Property::factory()->for($customer)->cancelled()->count(1)->create();

        $this->action->execute($customer);

        $this->assertEquals(CustomerStatus::Paused, $customer->fresh()->status);
    }

    public function test_customer_status_remains_cancelled_when_no_properties_exist(): void
    {
        $customer = Customer::factory()->create([
            'status' => 'cancelled',
        ]);

        $this->action->execute($customer);

        $this->assertEquals(CustomerStatus::Cancelled, $customer->fresh()->status);
    }
}
