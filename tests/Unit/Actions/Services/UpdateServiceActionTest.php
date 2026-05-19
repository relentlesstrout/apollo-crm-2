<?php

namespace Tests\Unit\Actions\Services;

use App\Actions\Services\UpdateServiceAction;
use App\DTOs\Service\ServiceData;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateServiceActionTest extends TestCase
{
    use RefreshDatabase;

    private UpdateServiceAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new UpdateServiceAction;
    }

    public function test_it_updates_a_service(): void
    {
        $service = Service::factory()->create([
            'name' => 'Old Name',
            'description' => null,
        ]);

        $this->action->execute($this->makeServiceData(), $service);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Window Clean',
            'description' => 'Clean all accessible windows inside and out.',
        ]);
    }

    public function test_it_clears_a_description(): void
    {
        $service = Service::factory()->create([
            'description' => 'Old description.',
        ]);

        $this->action->execute($this->makeServiceData(description: null), $service);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'description' => null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeServiceData(
        string $name = 'Window Clean',
        ?string $description = 'Clean all accessible windows inside and out.',
    ): ServiceData {
        return new ServiceData(
            name: $name,
            description: $description,
        );
    }
}
