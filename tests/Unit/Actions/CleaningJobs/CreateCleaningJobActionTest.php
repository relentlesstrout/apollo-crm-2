<?php

namespace Tests\Unit\Actions\CleaningJobs;

use App\Actions\CleaningJobs\CreateCleaningJobAction;
use App\DTOs\CleaningJob\CleaningJobData;
use App\Enums\CleaningJobStatus;
use App\Enums\UserRole;
use App\Models\Property;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateCleaningJobActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateCleaningJobAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new CreateCleaningJobAction;
    }

    public function test_it_creates_a_scheduled_cleaning_job(): void
    {
        $property = Property::factory()->create();

        $job = $this->action->execute($property, $this->makeCleaningJobData());

        $this->assertDatabaseHas('cleaning_jobs', [
            'id' => $job->id,
            'property_id' => $property->id,
            'status' => CleaningJobStatus::Scheduled->value,
            'notes' => 'Front and back',
            'scheduled_at' => '2026-07-10 00:00:00',
        ]);
    }

    public function test_it_attaches_services_with_their_expected_price(): void
    {
        $property = Property::factory()->create();
        $service = Service::factory()->create();

        $job = $this->action->execute($property, $this->makeCleaningJobData(
            services: [['service_id' => $service->id, 'price' => 3000]],
        ));

        $this->assertDatabaseHas('cleaning_job_service', [
            'cleaning_job_id' => $job->id,
            'service_id' => $service->id,
            'price' => 3000,
            'actual_price' => null,
        ]);
    }

    public function test_it_assigns_cleaners(): void
    {
        $property = Property::factory()->create();
        $cleaner = User::factory()->create(['role' => UserRole::Cleaner]);

        $job = $this->action->execute($property, $this->makeCleaningJobData(
            cleanerIds: [$cleaner->id],
        ));

        $this->assertDatabaseHas('cleaning_job_user', [
            'cleaning_job_id' => $job->id,
            'user_id' => $cleaner->id,
        ]);
    }

    public function test_it_returns_the_created_job(): void
    {
        $property = Property::factory()->create();

        $job = $this->action->execute($property, $this->makeCleaningJobData());

        $this->assertEquals($property->id, $job->property_id);
        $this->assertEquals(CleaningJobStatus::Scheduled, $job->status);
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
        ?string $notes = 'Front and back',
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
