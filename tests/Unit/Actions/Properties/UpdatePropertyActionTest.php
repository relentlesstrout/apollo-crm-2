<?php

namespace Tests\Unit\Actions\Properties;

use App\Actions\Customers\RecomputeCustomerStatusAction;
use App\Actions\Properties\UpdatePropertyAction;
use App\DTOs\Property\PropertyData;
use App\Enums\PropertyStatus;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdatePropertyActionTest extends TestCase
{
    use RefreshDatabase;

    private UpdatePropertyAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new UpdatePropertyAction(new RecomputeCustomerStatusAction);
    }

    public function test_it_updates_a_property(): void
    {
        $property = Property::factory()->create([
            'house' => 'Old House',
            'street' => 'Old Street',
            'area' => 'Ryton',
            'postcode' => 'NE40 3AA',
            'notes' => null,
        ]);

        $this->action->execute($this->makePropertyData(), $property);

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'house' => '12',
            'street' => 'High Street',
            'area' => 'Whickham',
            'postcode' => 'NE16 4AA',
            'notes' => 'Test notes.',
        ]);
    }

    public function test_it_does_not_change_the_property_status(): void
    {
        $property = Property::factory()->paused()->create();

        $this->action->execute($this->makePropertyData(), $property);

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'status' => PropertyStatus::Paused,
        ]);
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
