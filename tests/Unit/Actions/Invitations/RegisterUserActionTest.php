<?php

namespace Tests\Unit\Actions\Invitations;

use App\Actions\Invitations\RegisterUserAction;
use App\DTOs\User\UserData;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_user_from_user_data(): void
    {
        $data = new UserData(
            name: 'Matthew Smith',
            phone: '+441632960000',
            email: 'matthew@example.com',
            password: 'password',
            role: UserRole::Admin,
        );

        $user = (new RegisterUserAction)->execute($data);

        $this->assertNotNull($user->id);

        $this->assertDatabaseHas('users', [
            'name'  => 'Matthew Smith',
            'phone' => '+441632960000',
            'email' => 'matthew@example.com',
            'role'  => UserRole::Admin,
        ]);
    }
}
