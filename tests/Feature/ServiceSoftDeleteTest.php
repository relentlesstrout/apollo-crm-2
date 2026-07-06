<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\CleaningJob;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_a_service_soft_deletes_it(): void
    {
        $service = Service::factory()->create();

        $service->delete();

        $this->assertSoftDeleted($service);
    }

    public function test_deleting_a_service_retains_job_price_snapshots(): void
    {
        $job = CleaningJob::factory()->create();
        $service = Service::factory()->create();
        $job->services()->attach($service->id, ['price' => 3000]);

        $service->delete();

        $this->assertDatabaseHas('cleaning_job_service', [
            'cleaning_job_id' => $job->id,
            'service_id' => $service->id,
            'price' => 3000,
        ]);
    }

    public function test_a_retired_service_still_shows_on_a_historical_job(): void
    {
        $job = CleaningJob::factory()->create();
        $service = Service::factory()->create(['name' => 'Conservatory Clean']);
        $job->services()->attach($service->id, ['price' => 3000]);

        $service->delete();

        $job->refresh()->load('services');
        $this->assertCount(1, $job->services);
        $this->assertEquals('Conservatory Clean', $job->services->first()->name);
        $this->assertEquals(3000, $job->services->first()->pivot->price);
    }

    public function test_retired_services_are_excluded_from_default_queries(): void
    {
        $active = Service::factory()->create();
        $retired = Service::factory()->create();
        $retired->delete();

        $ids = Service::query()->pluck('id');

        $this->assertTrue($ids->contains($active->id));
        $this->assertFalse($ids->contains($retired->id));
    }

    public function test_schedule_service_relation_resolves_a_retired_service(): void
    {
        $service = Service::factory()->create(['name' => 'Gutter Clean']);
        $schedule = Schedule::factory()->create(['service_id' => $service->id]);

        $service->delete();

        $this->assertEquals('Gutter Clean', $schedule->fresh()->service->name);
    }

    public function test_admin_deleting_a_service_soft_deletes_via_the_controller(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $service = Service::factory()->create();

        $this->actingAs($admin)
            ->delete(route('services.destroy', $service))
            ->assertRedirect(route('services.index'));

        $this->assertSoftDeleted($service);
    }
}
