<?php

namespace App\Actions\Customers;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Password;

class GrantCustomerPortalAccessAction
{
    public function execute(Customer $customer): void
    {
        $user = User::create([
            'name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'password' => bcrypt(str()->random(32)),
            'role' => UserRole::Customer,
        ]);

        $customer->update(['user_id' => $user->id]);

        Password::sendResetLink(['email' => $user->email]);
    }
}
