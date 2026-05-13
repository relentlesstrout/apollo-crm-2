<?php

namespace Tests\Unit\Actions\Customers;

use App\Actions\Customers\GrantCustomerPortalAccessAction;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class GrantCustomerPortalAccessActionTest extends TestCase
{
    use RefreshDatabase;

    private GrantCustomerPortalAccessAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->action = new GrantCustomerPortalAccessAction();
    }

    public function test_it_creates_a_user_with_the_customer_details(): void
    {
        $customer = Customer::factory()->create([
            'name'  => 'Test Customer',
            'phone' => '0786412454',
            'email' => 'customer@example.com',
        ]);

        $this->action->execute($customer);

        $this->assertDatabaseHas('users', [
            'name'  => 'Test Customer',
            'phone' => '0786412454',
            'email' => 'customer@example.com',
            'role'  => UserRole::Customer,
        ]);
    }

    public function test_it_links_the_created_user_to_the_customer(): void
    {
        $customer = Customer::factory()->create(['email' => 'customer@example.com']);

        $this->action->execute($customer);

        $user = User::where('email', 'customer@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame($user->id, $customer->fresh()->user_id);
    }

    public function test_it_assigns_a_hashed_random_password(): void
    {
        $customer = Customer::factory()->create(['email' => 'customer@example.com']);

        $this->action->execute($customer);

        $user = User::where('email', 'customer@example.com')->first();

        $this->assertNotEmpty($user->password);
        $this->assertNotSame('', $user->password);
        $this->assertFalse(Hash::needsRehash($user->password));
    }

    public function test_it_sends_a_password_reset_notification_to_the_new_user(): void
    {
        $customer = Customer::factory()->create(['email' => 'customer@example.com']);

        $this->action->execute($customer);

        $user = User::where('email', 'customer@example.com')->first();

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
