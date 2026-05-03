<?php

namespace App\DTOs\Invitation;

use App\Enums\UserRole;
use App\Http\Requests\InviteRequest;

class InvitationData
{
    public function __construct(
        public readonly string $email,
        public readonly UserRole $role,
        public readonly int $invitedById,
    ) {}

    public static function fromRequest(InviteRequest $request): self
    {
        return new self(
            email: $request->email,
            role: UserRole::from($request->role),
            invitedById: $request->user()->id,
        );
    }
}
