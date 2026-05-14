<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Property;
use App\Models\User;
use App\Policies\PropertyPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyPolicyTest extends TestCase
{
    use RefreshDatabase;

    private PropertyPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new PropertyPolicy;
    }

    public function test_admin_can_view_any_properties(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->assertTrue($this->policy->viewAny($admin));
    }

    public function test_non_admin_cannot_view_any_properties(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $this->assertFalse($this->policy->viewAny($customer));
    }

    public function test_admin_can_view_a_property(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $property = Property::factory()->create();

        $this->assertTrue($this->policy->view($admin, $property));
    }

    public function test_non_admin_cannot_view_a_property(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);
        $property = Property::factory()->create();

        $this->assertFalse($this->policy->view($customer, $property));
    }

    public function test_admin_can_create_properties(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->assertTrue($this->policy->create($admin));
    }

    public function test_non_admin_cannot_create_properties(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $this->assertFalse($this->policy->create($customer));
    }

    public function test_admin_can_update_a_property(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $property = Property::factory()->create();

        $this->assertTrue($this->policy->update($admin, $property));
    }

    public function test_non_admin_cannot_update_a_property(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);
        $property = Property::factory()->create();

        $this->assertFalse($this->policy->update($customer, $property));
    }
}
