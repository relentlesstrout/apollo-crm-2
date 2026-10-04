<?php

namespace Tests\Unit\Actions\Services;

use App\Actions\Services\ArchiveServiceAction;
use App\Actions\Services\RestoreServiceAction;
use App\Models\PropertyService;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestoreServiceActionTest extends TestCase
{
    use RefreshDatabase;

    private RestoreServiceAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new RestoreServiceAction;
    }

    public function test_it_restores_the_service(): void
    {
        $service = Service::factory()->create();
        (new ArchiveServiceAction)->execute($service);

        $this->action->execute($service->fresh());

        $this->assertNotSoftDeleted($service);
    }

    public function test_it_restores_the_prices_that_were_archived_with_the_service(): void
    {
        $service = Service::factory()->create();
        $first = PropertyService::factory()->create(['service_id' => $service->id]);
        $second = PropertyService::factory()->create(['service_id' => $service->id]);
        (new ArchiveServiceAction)->execute($service);

        $restored = $this->action->execute($service->fresh());

        $this->assertSame(2, $restored);
        $this->assertNotSoftDeleted($first);
        $this->assertNotSoftDeleted($second);
    }

    public function test_it_does_not_restore_a_price_that_was_removed_before_the_archive(): void
    {
        $service = Service::factory()->create();
        $removedEarlier = PropertyService::factory()->create(['service_id' => $service->id]);
        $removedEarlier->delete();
        $this->travel(1)->minutes();
        $archivedWithService = PropertyService::factory()->create(['service_id' => $service->id]);
        (new ArchiveServiceAction)->execute($service);

        $restored = $this->action->execute($service->fresh());

        $this->assertSame(1, $restored);
        $this->assertSoftDeleted($removedEarlier);
        $this->assertNotSoftDeleted($archivedWithService);
    }

    public function test_it_leaves_the_schedules_switched_off(): void
    {
        $service = Service::factory()->create();
        $schedule = Schedule::factory()->create(['service_id' => $service->id]);
        (new ArchiveServiceAction)->execute($service);

        $this->action->execute($service->fresh());

        $this->assertNull($schedule->fresh()->active_at);
    }
}
