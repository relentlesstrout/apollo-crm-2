<?php

namespace Tests\Unit\Actions\Invitations;

use App\Actions\Invitations\InviteUserAction;
use App\DTOs\Invitation\InvitationData;
use App\Enums\InviteStatus;
use App\Enums\UserRole;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InviteUserActionTest extends TestCase
{
    use RefreshDatabase;

    private InviteUserAction $action;
    private User $inviter;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->action  = new InviteUserAction();
        $this->inviter = User::factory()->create();
    }

    public function test_pending_invitations_are_expired_before_new_invite_is_sent(): void
    {
        Invitation::factory()->create([
            'email'  => 'test@example.com',
            'status' => InviteStatus::Pending,
        ]);

        $this->action->execute($this->makeInvitationData('test@example.com'));

        $this->assertDatabaseHas('invitations', [
            'email'  => 'test@example.com',
            'status' => InviteStatus::Expired,
        ]);
    }

    public function test_only_pending_invitations_are_expired_not_other_statuses(): void
    {
        Invitation::factory()->create([
            'email'  => 'test@example.com',
            'status' => InviteStatus::Accepted,
        ]);

        $this->action->execute($this->makeInvitationData('test@example.com'));

        $this->assertDatabaseHas('invitations', [
            'email'  => 'test@example.com',
            'status' => InviteStatus::Accepted,
        ]);
    }

    public function test_new_invitation_is_created_when_none_exists(): void
    {
        $this->action->execute($this->makeInvitationData('newuser@example.com', UserRole::Cleaner));

        $this->assertDatabaseHas('invitations', [
            'email'      => 'newuser@example.com',
            'role'       => UserRole::Cleaner,
            'invited_by' => $this->inviter->id,
            'status'     => InviteStatus::Pending,
        ]);
    }

    public function test_a_new_invitation_record_is_created_preserving_invite_history(): void
    {
        Invitation::factory()->create([
            'email'  => 'existing@example.com',
            'role'   => UserRole::Cleaner,
            'status' => InviteStatus::Expired,
        ]);

        $this->action->execute($this->makeInvitationData('existing@example.com', UserRole::Cleaner));

        $this->assertCount(2, Invitation::where('email', 'existing@example.com')->get());

        $this->assertDatabaseHas('invitations', [
            'email'  => 'existing@example.com',
            'role'   => UserRole::Cleaner,
            'status' => InviteStatus::Pending,
        ]);

        $this->assertDatabaseHas('invitations', [
            'email'  => 'existing@example.com',
            'role'   => UserRole::Cleaner,
            'status' => InviteStatus::Expired,
        ]);
    }

    public function test_invitation_mail_is_sent_to_the_correct_address(): void
    {
        $this->action->execute($this->makeInvitationData('recipient@example.com'));

        Mail::assertSent(InvitationMail::class, function (InvitationMail $mail): bool {
            return $mail->hasTo('recipient@example.com');
        });
    }

    public function test_mail_is_sent_exactly_once(): void
    {
        $this->action->execute($this->makeInvitationData('recipient@example.com'));

        Mail::assertSentCount(1);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeInvitationData(string $email, UserRole $role = UserRole::Admin ): InvitationData
    {
        return new InvitationData(
            email:        $email,
            role:         $role,
            invitedById:  $this->inviter->id,
        );
    }
}
