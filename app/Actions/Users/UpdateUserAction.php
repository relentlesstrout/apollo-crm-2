<?php

namespace App\Actions\Users;

use App\DTOs\User\UserData;
use App\Models\User;

class UpdateUserAction
{
    public function execute( UserData $data, User $user): bool
    {
        return User::update([
            'name' => $data->name,
            'phone' => $data->phone,
            'email' => $data->email,
            'password' => $data->password ?? $user->password,
            'role' => $data->role,
        ]);
    }
}
