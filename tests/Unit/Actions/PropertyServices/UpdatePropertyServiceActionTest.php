<?php

namespace Tests\Unit\Actions\PropertyServices;

use App\Actions\PropertyServices\UpdatePropertyServiceAction;
use App\DTOs\PropertyService\PropertyServiceData;
use App\Models\PropertyService;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdatePropertyServiceActionTest extends TestCase
{
    use RefreshDatabase;

    private UpdatePropertyServiceAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new UpdatePropertyServiceAction;
    }

    public function test_it_updates_a_property_service(): void
    {
        $propertyService = PropertyService::factory()->create([
            'price' => 1000,
            'description' => null,
            'effective_from' => '2025-01-01',
            'effective_to' => null,
        ]);
        $newService = Service::factory()->create();

        $this->action->execute($this->makePropertyServiceData(serviceId: $newService->id), $propertyService);

        $this->assertDatabaseHas('property_service', [
            'id' => $propertyService->id,
            'service_id' => $newService->id,
            'price' => 1500,
            'description' => 'Clean all windows on the ground floor only.',
            'effective_from' => '2026-01-01',
            'effective_to' => null,
        ]);
    }

    public function test_it_sets_an_end_date(): void
    {
        $propertyService = PropertyService::factory()->create(['effective_to' => null]);

        $this->action->execute($this->makePropertyServiceData(
            serviceId: $propertyService->service_id,
            effectiveTo: '2026-12-31',
        ), $propertyService);

        $this->assertDatabaseHas('property_service', [
            'id' => $propertyService->id,
            'effective_to' => '2026-12-31',
        ]);
    }

    public function test_it_clears_a_description(): void
    {
        $propertyService = PropertyService::factory()->create(['description' => 'Old description.']);

        $this->action->execute($this->makePropertyServiceData(
            serviceId: $propertyService->service_id,
            description: null,
        ), $propertyService);

        $this->assertDatabaseHas('property_service', [
            'id' => $propertyService->id,
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
