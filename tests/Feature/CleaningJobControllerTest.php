<?php

namespace Tests\Feature;

use App\Enums\CleaningJobStatus;
use App\Enums\UserRole;
use App\Models\CleaningJob;
use App\Models\Property;
use App\Models\PropertyService;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CleaningJobControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::Admin]);
    }

    /**
     * @return array{0: Property, 1: Service}
     */
    private function propertyWithService(): array
    {
        $property = Property::factory()->create();
        $service = Service::factory()->create();
        PropertyService::factory()->create([
            'property_id' => $property->id,
            'service_id' => $service->id,
            'price' => 2000,
        ]);

        return [$property, $service];
    }

    public function test_admin_can_create_a_cleaning_job(): void
    {
        [$property, $service] = $this->propertyWithService();
        $cleaner = User::factory()->create(['role' => UserRole::Cleaner]);

        $response = $this->actingAs($this->admin())->post(
            route('properties.cleaning-jobs.store', $property),
            [
                'scheduled_at' => '2026-07-10',
                'notes' => 'Front and back',
                'services' => [$service->id],
                'prices' => [$service->id => '30.00'],
                'cleaners' => [$cleaner->id],
            ],
        );

        $job = CleaningJob::firstOrFail();
        $response->assertRedirect(route('cleaning-jobs.show', $job));

        $this->assertDatabaseHas('cleaning_jobs', [
            'id' => $job->id,
            'property_id' => $property->id,
            'status' => CleaningJobStatus::Scheduled->value,
        ]);
        $this->assertDatabaseHas('cleaning_job_service', [
            'cleaning_job_id' => $job->id,
            'service_id' => $service->id,
            'price' => 3000,
        ]);
        $this->assertDatabaseHas('cleaning_job_user', [
            'cleaning_job_id' => $job->id,
            'user_id' => $cleaner->id,
        ]);
    }

    public function test_non_admin_cannot_create_a_cleaning_job(): void
    {
        [$property, $service] = $this->propertyWithService();
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $response = $this->actingAs($customer)->post(
            route('properties.cleaning-jobs.store', $property),
            [
                'scheduled_at' => '2026-07-10',
                'services' => [$service->id],
                'prices' => [$service->id => '30.00'],
            ],
        );

        $response->assertForbidden();
        $this->assertDatabaseCount('cleaning_jobs', 0);
    }

    public function test_it_requires_at_least_one_service(): void
    {
        [$property] = $this->propertyWithService();

        $response = $this->actingAs($this->admin())->post(
            route('properties.cleaning-jobs.store', $property),
            ['scheduled_at' => '2026-07-10'],
        );

        $response->assertSessionHasErrors('services');
        $this->assertDatabaseCount('cleaning_jobs', 0);
    }

    public function test_it_requires_a_price_for_each_selected_service(): void
    {
        [$property, $service] = $this->propertyWithService();

        $response = $this->actingAs($this->admin())->post(
            route('properties.cleaning-jobs.store', $property),
            [
                'scheduled_at' => '2026-07-10',
                'services' => [$service->id],
                'prices' => [],
            ],
        );

        $response->assertSessionHasErrors("prices.{$service->id}");
        $this->assertDatabaseCount('cleaning_jobs', 0);
    }

    public function test_a_service_must_belong_to_the_property(): void
    {
        [$property] = $this->propertyWithService();
        $foreignService = Service::factory()->create();

        $response = $this->actingAs($this->admin())->post(
            route('properties.cleaning-jobs.store', $property),
            [
                'scheduled_at' => '2026-07-10',
                'services' => [$foreignService->id],
                'prices' => [$foreignService->id => '10.00'],
            ],
        );

        $response->assertSessionHasErrors('services.0');
        $this->assertDatabaseCount('cleaning_jobs', 0);
    }

    public function test_admin_can_update_a_cleaning_job(): void
    {
        [$property, $service] = $this->propertyWithService();
        $job = CleaningJob::factory()->for($property)->create(['notes' => 'Old']);

        $response = $this->actingAs($this->admin())->put(
            route('cleaning-jobs.update', $job),
            [
                'scheduled_at' => '2026-08-01',
                'notes' => 'Updated',
                'services' => [$service->id],
                'prices' => [$service->id => '45.00'],
            ],
        );

        $response->assertRedirect(route('cleaning-jobs.show', $job));
        $this->assertDatabaseHas('cleaning_jobs', ['id' => $job->id, 'notes' => 'Updated']);
        $this->assertDatabaseHas('cleaning_job_service', [
            'cleaning_job_id' => $job->id,
            'service_id' => $service->id,
            'price' => 4500,
        ]);
    }

    public function test_admin_can_transition_status(): void
    {
        $job = CleaningJob::factory()->create();

        $response = $this->actingAs($this->admin())->post(
            route('cleaning-jobs.status', $job),
            ['status' => CleaningJobStatus::InProgress->value],
        );

        $response->assertRedirect(route('cleaning-jobs.show', $job));
        $job->refresh();
        $this->assertEquals(CleaningJobStatus::InProgress, $job->status);
        $this->assertNotNull($job->started_at);
    }

    public function test_index_is_accessible_to_admin(): void
    {
        $this->actingAs($this->admin())
            ->get(route('cleaning-jobs.index'))
            ->assertOk();
    }

    public function test_show_is_accessible_to_admin(): void
    {
        $job = CleaningJob::factory()->create();

        $this->actingAs($this->admin())
            ->get(route('cleaning-jobs.show', $job))
            ->assertOk();
    }

    public function test_non_admin_cannot_view_the_index(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $this->actingAs($customer)
            ->get(route('cleaning-jobs.index'))
            ->assertForbidden();
    }
}
