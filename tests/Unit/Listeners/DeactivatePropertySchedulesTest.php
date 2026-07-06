<?php

namespace Tests\Unit\Listeners;

use App\Events\PropertyCancelled;
use App\Listeners\DeactivatePropertySchedules;
use App\Models\Property;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeactivatePropertySchedulesTest extends TestCase
{
    use RefreshDatabase;

    private DeactivatePropertySchedules $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->listener = new DeactivatePropertySchedules;
    }

    public function test_it_deactivates_all_schedules_on_the_property(): void
    {
        $property = Property::factory()->create();
        Schedule::factory()->count(3)->for($property)->create(['active_at' => now()]);

        $this->listener->handle(new PropertyCancelled($property));

        $this->assertEquals(0, $property->schedules()->whereNotNull('active_at')->count());
    }

    public function test_it_does_not_affect_schedules_on_other_properties(): void
    {
        $cancelledProperty = Property::factory()->create();
        $otherProperty = Property::factory()->create();
        Schedule::factory()->for($cancelledProperty)->create(['active_at' => now()]);
        Schedule::factory()->for($otherProperty)->create(['active_at' => now()]);

        $this->listener->handle(new PropertyCancelled($cancelledProperty));

        $this->assertNotNull($otherProperty->schedules()->first()->active_at);
    }

    public function test_it_is_a_no_op_when_property_has_no_schedules(): void
    {
        $property = Property::factory()->create();

        $this->listener->handle(new PropertyCancelled($property));

        $this->assertEquals(0, $property->schedules()->count());
    }

    public function test_it_is_idempotent_on_already_inactive_schedules(): void
    {
        $property = Property::factory()->create();
        Schedule::factory()->for($property)->create(['active_at' => null]);

        $this->listener->handle(new PropertyCancelled($property));

        $this->assertNull($property->schedules()->first()->active_at);
    }
}
