<?php

namespace App\Actions\Invitations;

use App\DTOs\Invitation\InvitationData;
use App\Enums\InviteStatus;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use Illuminate\Support\Facades\Mail;

class InviteUserAction
{
    public function execute(InvitationData $data): void
    {
        Invitation::where('email', $data->email)
            ->where('status', InviteStatus::Pending)
            ->update(['status' => InviteStatus::Expired]);

        $invitation = Invitation::create(
            [
                'email' => $data->email,
                'token' => bin2hex(random_bytes(32)),
                'role' => $data->role,
                'invited_by' => $data->invitedById,
                'accepted_at' => null,
                'status' => InviteStatus::Pending,
                'expires_at' => now()->addDays(7),
            ]
        );

        Mail::to($data->email)->send(new InvitationMail($invitation));
    }
}
