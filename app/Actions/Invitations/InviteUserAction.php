<?php

namespace App\Actions\Invitations;

use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class InviteUserAction
{
    public function execute(string $email, string $role, User $invitedBy): Invitation
    {
        $invitation = Invitation::updateOrCreate([
            ['email' => $email],
            [
                'token' => bin2hex(random_bytes(32)),
                'role' => $role,
                'invited_by' => $invitedBy->id,
                'accepted_at' => null,
            ]
        ]);

        Mail::to($email)->send(new InvitationMail($invitation));
    }
}
