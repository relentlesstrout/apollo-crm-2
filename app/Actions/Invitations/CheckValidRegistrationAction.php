<?php

namespace App\Actions\Invitations;

use App\Enums\UserRole;
use App\Http\Requests\RegisterRequest;
use App\Models\Invitation;

class CheckValidRegistrationAction
{
    public static function execute(RegisterRequest $request, string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('expires_at', '>', now()->format('Y-m-d H:i:s'))
            ->whereNull('accepted_at')
            ->firstOrFail();

        if ($invitation->email !== $request->email) {
            return redirect()->back()->withErrors(['email' => 'The email does not match the invitation.']);
        }

        if ($invitation->role->value !== $request->role) {
            return redirect()->back()->withErrors(['role' => 'The role does not match the invitation.']);
        }
        return $invitation;
    }

}
