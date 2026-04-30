<?php

namespace App\Actions\Invitations;

use App\DTOs\Invitation\InvitationData;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class InviteUserAction
{
    public function execute(InvitationData $data): void
    {
        Invitation::where('email', $data->email)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        $invitation = Invitation::updateOrCreate(
            ['email' => $data->email],
            [
                'token' => bin2hex(random_bytes(32)),
                'role' => $data->role,
                'invited_by' => $data->invitedById,
                'accepted_at' => null,
                'status' => 'pending',
                'expires_at' => now()->addDays(7),
            ]
        );

        Mail::to($data->email)->send(new InvitationMail($invitation));
    }
}
