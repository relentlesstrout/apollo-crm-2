<?php

namespace Tests\Unit\Actions\Invitations;

use InvalidArgumentException;
use Tests\TestCase;
use App\Models\Invitation;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Actions\Invitations\CheckValidRegistrationAction;
use App\Http\Requests\RegisterRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

class CheckValidRegistrationActionTest extends TestCase
{
    use RefreshDatabase;
    private function makeRequest(array $data): RegisterRequest
    {
        $request = new RegisterRequest();
        $request->merge($data);

        return $request;
    }

    /** @test */
    public function test_it_fails_for_invalid_token()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid, expired, or already accepted invitation.');

        $request = $this->makeRequest([
            'email' => 'test@example.com',
            'role' => UserRole::Admin->value,
        ]);

        CheckValidRegistrationAction::execute($request, 'invalid-token');
    }

    /** @test */
    public function test_it_fails_for_expired_invitation()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid, expired, or already accepted invitation.');


        Invitation::factory()->create([
            'token' => 'token',
            'expires_at' => Carbon::now()->subDay(),
            'accepted_at' => null,
        ]);

        $request = $this->makeRequest([
            'email' => 'test@example.com',
            'role' => UserRole::Admin->value,
        ]);

        CheckValidRegistrationAction::execute($request, 'token');
    }

    /** @test */
    public function test_it_fails_for_accepted_invitation()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid, expired, or already accepted invitation.');

        Invitation::factory()->create([
            'token' => 'token',
            'expires_at' => Carbon::now()->addDay(),
            'accepted_at' => Carbon::now(),
        ]);

        $request = $this->makeRequest([
            'email' => 'test@example.com',
            'role' => UserRole::Admin->value,
        ]);

        CheckValidRegistrationAction::execute($request, 'token');
    }

    /** @test */
    public function test_it_fails_when_email_does_not_match()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The email does not match the invitation.');

        $invitation = Invitation::factory()->create([
            'token' => 'token',
            'expires_at' => Carbon::now()->addDay(),
            'accepted_at' => null,
            'email' => 'correct@example.com',
        ]);

        $request = $this->makeRequest([
            'email' => 'wrong@example.com',
            'role' => $invitation->role->value,
        ]);

        CheckValidRegistrationAction::execute($request, 'token');
    }

    /** @test */
    public function test_it_fails_when_role_does_not_match()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The role does not match the invitation.');

        $invitation = Invitation::factory()->create([
            'token' => 'token',
            'expires_at' => Carbon::now()->addDay(),
            'accepted_at' => null,
            'role' => UserRole::Admin->value,
        ]);

        $request = $this->makeRequest([
            'email' => $invitation->email,
            'role' => UserRole::Cleaner->value,
        ]);

        CheckValidRegistrationAction::execute($request, 'token');
    }

    /** @test */
    public function test_it_returns_invitation_on_happy_path()
    {
        $invitation = Invitation::factory()->create([
            'token' => 'token',
            'expires_at' => Carbon::now()->addDay(),
            'accepted_at' => null,
        ]);

        $request = $this->makeRequest([
            'email' => $invitation->email,
            'role' => $invitation->role->value,
        ]);

        $result = CheckValidRegistrationAction::execute($request, 'token');

        $this->assertInstanceOf(Invitation::class, $result);
        $this->assertEquals($invitation->id, $result->id);
    }
}
