<?php

namespace Tests\Unit\Actions\User;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdateUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_user_be_updated()
    {
        $user = User::create([
            'name' => 'Test User',
            'phone' => '0786412454',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => UserRole::Admin,
        ]);

        $user->update([
            'name' => 'New Test User',
            'phone' => '0786412453',
            'email' => 'newtest@example.com',
            'role' => UserRole::Admin,
        ]);

        $this->assertSame('New Test User', $user->name);
        $this->assertSame('0786412453', $user->phone);
        $this->assertSame('newtest@example.com', $user->email);
        $this->assertSame(UserRole::Admin, $user->role);
    }

    public function test_user_password_is_hashed()
    {
        $user = User::create([
            'name' => 'Test User',
            'phone' => '0786412454',
            'email' => 'test@example.com',
            'password' => 'secret',
            'role' => 'admin',
        ]);

         $this->assertNotEquals('secret', $user->password);
         $this->assertTrue(Hash::check('secret', $user->password));
    }
}
