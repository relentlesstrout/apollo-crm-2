<?php

namespace Tests\Unit\Actions\CleaningJobs;

use App\Actions\CleaningJobs\UpdateCleaningJobAction;
use App\DTOs\CleaningJob\CleaningJobData;
use App\Enums\UserRole;
use App\Models\CleaningJob;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCleaningJobActionTest extends TestCase
{
    use RefreshDatabase;

    private UpdateCleaningJobAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new UpdateCleaningJobAction;
    }

    public function test_it_updates_the_job_fields(): void
    {
        $job = CleaningJob::factory()->create([
            'notes' => 'Old note',
            'scheduled_at' => '2026-07-01',
        ]);

        $this->action->execute($this->makeCleaningJobData(
            scheduledAt: '2026-08-15',
            notes: 'New note',
        ), $job);

        $this->assertDatabaseHas('cleaning_jobs', [
            'id' => $job->id,
            'notes' => 'New note',
            'scheduled_at' => '2026-08-15 00:00:00',
        ]);
    }

    public function test_it_syncs_services_and_their_price(): void
    {
        $job = CleaningJob::factory()->create();
        $keep = Service::factory()->create();
        $remove = Service::factory()->create();
        $job->services()->attach([$remove->id => ['price' => 1000]]);

        $this->action->execute($this->makeCleaningJobData(
            services: [['service_id' => $keep->id, 'price' => 2500]],
        ), $job);

        $this->assertDatabaseHas('cleaning_job_service', [
            'cleaning_job_id' => $job->id,
            'service_id' => $keep->id,
            'price' => 2500,
        ]);
        $this->assertDatabaseMissing('cleaning_job_service', [
            'cleaning_job_id' => $job->id,
            'service_id' => $remove->id,
        ]);
    }

    public function test_it_preserves_actual_price_on_retained_services(): void
    {
        $job = CleaningJob::factory()->create();
        $service = Service::factory()->create();
        $job->services()->attach([$service->id => ['price' => 2000, 'actual_price' => 2200]]);

        $this->action->execute($this->makeCleaningJobData(
            services: [['service_id' => $service->id, 'price' => 3000]],
        ), $job);

        $this->assertDatabaseHas('cleaning_job_service', [
            'cleaning_job_id' => $job->id,
            'service_id' => $service->id,
            'price' => 3000,
            'actual_price' => 2200,
        ]);
    }

    public function test_it_syncs_assigned_cleaners(): void
    {
        $job = CleaningJob::factory()->create();
        $previous = User::factory()->create(['role' => UserRole::Cleaner]);
        $next = User::factory()->create(['role' => UserRole::Cleaner]);
        $job->cleaners()->attach($previous->id);

        $this->action->execute($this->makeCleaningJobData(
            cleanerIds: [$next->id],
        ), $job);

        $this->assertDatabaseHas('cleaning_job_user', [
            'cleaning_job_id' => $job->id,
            'user_id' => $next->id,
        ]);
        $this->assertDatabaseMissing('cleaning_job_user', [
            'cleaning_job_id' => $job->id,
            'user_id' => $previous->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * @param  array<int, array{service_id: int, price: int}>  $services
     * @param  array<int, int>  $cleanerIds
     */
    private function makeCleaningJobData(
        string $scheduledAt = '2026-07-10',
        ?string $notes = 'Notes',
        array $services = [],
        array $cleanerIds = [],
    ): CleaningJobData {
        return new CleaningJobData(
            scheduledAt: $scheduledAt,
            notes: $notes,
            services: $services,
            cleanerIds: $cleanerIds,
        );
    }
}
