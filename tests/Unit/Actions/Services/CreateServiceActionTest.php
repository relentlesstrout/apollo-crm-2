<?php

namespace Tests\Unit\Actions\Services;

use App\Actions\Services\CreateServiceAction;
use App\DTOs\Service\ServiceData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateServiceActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateServiceAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new CreateServiceAction;
    }

    public function test_it_creates_a_service(): void
    {
        $this->action->execute($this->makeServiceData());

        $this->assertDatabaseHas('services', [
            'name' => 'Window Clean',
            'description' => 'Clean all accessible windows inside and out.',
        ]);
    }

    public function test_it_creates_a_service_without_a_description(): void
    {
        $this->action->execute($this->makeServiceData(description: null));

        $this->assertDatabaseHas('services', [
            'name' => 'Window Clean',
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
