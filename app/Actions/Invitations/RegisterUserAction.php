<?php

namespace App\Actions\Invitations;

use App\DTOs\User\UserData;
use App\Models\User;

class RegisterUserAction
{
    public function execute(UserData $data) : User
    {
        return User::create([
            'name' => $data->name,
            'phone' => $data->phone,
            'email' => $data->email,
            'password' => $data->password,
            'role' => $data->role,
        ]);
    }
}
