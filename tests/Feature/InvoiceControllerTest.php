<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_send_invoice(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $response = $this->post(route('invoices.store', $customer));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_send_invoice(): void
    {
        $cleaner = User::factory()->create(['role' => UserRole::Cleaner]);
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $response = $this->actingAs($cleaner)->post(route('invoices.store', $customer));

        $response->assertForbidden();
    }

    public function test_admin_cannot_send_invoice_to_non_customer(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $cleaner = User::factory()->create(['role' => UserRole::Cleaner]);

        $response = $this->actingAs($admin)->post(route('invoices.store', $cleaner));

        $response->assertForbidden();
    }

    public function test_send_invoice_button_visible_for_customer_on_show_page(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $response = $this->actingAs($admin)->get(route('users.show', $customer));

        $response->assertStatus(200);
        $response->assertSee('Send Invoice');
    }

    public function test_send_invoice_button_not_visible_for_non_customer(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $cleaner = User::factory()->create(['role' => UserRole::Cleaner]);

        $response = $this->actingAs($admin)->get(route('users.show', $cleaner));

        $response->assertStatus(200);
        $response->assertDontSee('Send Invoice');
    }
}
