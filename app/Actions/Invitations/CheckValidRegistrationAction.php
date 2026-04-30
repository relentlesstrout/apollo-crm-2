<?php

namespace App\Actions\Invitations;

use App\Http\Requests\RegisterRequest;
use App\Models\Invitation;
use InvalidArgumentException;

class CheckValidRegistrationAction
{
    public static function execute(RegisterRequest $request, string $token): Invitation
    {
        $invitation = Invitation::query()
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->first();

        if (! $invitation) {
            throw new InvalidArgumentException(
                'Invalid, expired, or already accepted invitation.'
            );
        }

        if ($invitation->email !== $request->email) {
            throw new InvalidArgumentException(
                'The email does not match the invitation.'
            );
        }

        if ($invitation->role->value !== $request->role) {
            throw new InvalidArgumentException(
                'The role does not match the invitation.'
            );
        }

        return $invitation;
    }
}
