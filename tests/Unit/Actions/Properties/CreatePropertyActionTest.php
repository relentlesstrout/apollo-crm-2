<?php

namespace Tests\Unit\Actions\Properties;

use App\Actions\Properties\CreatePropertyAction;
use App\DTOs\Property\PropertyData;
use App\Enums\PropertyStatus;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePropertyActionTest extends TestCase
{
    use RefreshDatabase;

    private CreatePropertyAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new CreatePropertyAction;
    }

    public function test_it_creates_a_property_for_a_customer(): void
    {
        $customer = Customer::factory()->create();

        $property = $this->action->execute($customer, $this->makePropertyData());

        $this->assertNotNull($property->id);
        $this->assertDatabaseHas('properties', [
            'customer_id' => $customer->id,
            'house' => '12',
            'street' => 'High Street',
            'area' => 'Whickham',
            'postcode' => 'NE16 4AA',
            'notes' => 'Test notes.',
            'status' => PropertyStatus::Active,
        ]);
    }

    public function test_it_defaults_to_active_status(): void
    {
        $customer = Customer::factory()->create();

        $property = $this->action->execute($customer, $this->makePropertyData());

        $this->assertEquals(PropertyStatus::Active, $property->fresh()->status);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makePropertyData(
        string $house = '12',
        string $street = 'High Street',
        ?string $area = 'Whickham',
        string $postcode = 'NE16 4AA',
        ?string $notes = 'Test notes.',
    ): PropertyData {
        return new PropertyData(
            house: $house,
            street: $street,
            area: $area,
            postcode: $postcode,
            notes: $notes,
        );
    }
}
