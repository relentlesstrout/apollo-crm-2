<?php

namespace Tests\Unit\Actions\Properties;

use App\Actions\Customers\RecomputeCustomerStatusAction;
use App\Actions\Properties\UpdatePropertyStatusAction;
use App\Enums\CustomerStatus;
use App\Enums\PropertyStatus;
use App\Models\Customer;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdatePropertyStatusActionTest extends TestCase
{
    use RefreshDatabase;

    private UpdatePropertyStatusAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new UpdatePropertyStatusAction(new RecomputeCustomerStatusAction);
    }

    public function test_it_pauses_a_property(): void
    {
        $property = Property::factory()->create();

        $this->action->execute($property, PropertyStatus::Paused);

        $this->assertEquals(PropertyStatus::Paused, $property->fresh()->status);
    }

    public function test_it_cancels_a_property(): void
    {
        $property = Property::factory()->create();

        $this->action->execute($property, PropertyStatus::Cancelled);

        $this->assertEquals(PropertyStatus::Cancelled, $property->fresh()->status);
    }

    public function test_it_resumes_a_paused_property(): void
    {
        $property = Property::factory()->paused()->create();

        $this->action->execute($property, PropertyStatus::Active);

        $this->assertEquals(PropertyStatus::Active, $property->fresh()->status);
    }

    public function test_it_reactivates_a_cancelled_property(): void
    {
        $property = Property::factory()->cancelled()->create();

        $this->action->execute($property, PropertyStatus::Active);

        $this->assertEquals(PropertyStatus::Active, $property->fresh()->status);
    }

    public function test_it_recomputes_the_customer_status(): void
    {
        $customer = Customer::factory()->create();
        $property = Property::factory()->for($customer)->create();

        $this->action->execute($property, PropertyStatus::Paused);

        $this->assertEquals(CustomerStatus::Paused, $customer->fresh()->status);
    }

    public function test_customer_stays_active_when_one_property_remains_active(): void
    {
        $customer = Customer::factory()->create();
        $property = Property::factory()->for($customer)->create();
        Property::factory()->for($customer)->create();

        $this->action->execute($property, PropertyStatus::Paused);

        $this->assertEquals(CustomerStatus::Active, $customer->fresh()->status);
    }

    public function test_customer_becomes_cancelled_when_all_properties_are_cancelled(): void
    {
        $customer = Customer::factory()->create();
        $property = Property::factory()->for($customer)->create();

        $this->action->execute($property, PropertyStatus::Cancelled);

        $this->assertEquals(CustomerStatus::Cancelled, $customer->fresh()->status);
    }
}
