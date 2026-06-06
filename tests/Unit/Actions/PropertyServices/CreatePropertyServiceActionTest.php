<?php

namespace Tests\Unit\Actions\PropertyServices;

use App\Actions\PropertyServices\CreatePropertyServiceAction;
use App\DTOs\PropertyService\PropertyServiceData;
use App\Models\Property;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePropertyServiceActionTest extends TestCase
{
    use RefreshDatabase;

    private CreatePropertyServiceAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new CreatePropertyServiceAction;
    }

    public function test_it_creates_a_property_service(): void
    {
        $property = Property::factory()->create();
        $service = Service::factory()->create();

        $this->action->execute($property, $this->makePropertyServiceData(serviceId: $service->id));

        $this->assertDatabaseHas('property_service', [
            'property_id' => $property->id,
            'service_id' => $service->id,
            'price' => 1500,
            'description' => 'Clean all windows on the ground floor only.',
            'effective_from' => '2026-01-01',
            'effective_to' => null,
        ]);
    }

    public function test_it_creates_a_property_service_with_an_end_date(): void
    {
        $property = Property::factory()->create();
        $service = Service::factory()->create();

        $this->action->execute($property, $this->makePropertyServiceData(
            serviceId: $service->id,
            effectiveTo: '2026-12-31',
        ));

        $this->assertDatabaseHas('property_service', [
            'property_id' => $property->id,
            'effective_to' => '2026-12-31',
        ]);
    }

    public function test_it_creates_a_property_service_without_a_description(): void
    {
        $property = Property::factory()->create();
        $service = Service::factory()->create();

        $this->action->execute($property, $this->makePropertyServiceData(
            serviceId: $service->id,
            description: null,
        ));

        $this->assertDatabaseHas('property_service', [
            'property_id' => $property->id,
            'description' => null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makePropertyServiceData(
        int $serviceId = 1,
        int $price = 1500,
        ?string $description = 'Clean all windows on the ground floor only.',
        string $effectiveFrom = '2026-01-01',
        ?string $effectiveTo = null,
    ): PropertyServiceData {
        return new PropertyServiceData(
            serviceId: $serviceId,
            price: $price,
            description: $description,
            effectiveFrom: $effectiveFrom,
            effectiveTo: $effectiveTo,
        );
    }
}
