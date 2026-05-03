<?php

namespace App\Actions\Users;

use App\DTOs\User\UserData;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
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
