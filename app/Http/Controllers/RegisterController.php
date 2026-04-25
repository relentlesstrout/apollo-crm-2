<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function show (string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        return view('auth.register', compact('invitation'));
    }

    public function store (RegisterRequest $request, string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        $user = User::create([
            'name' => $request->name,
            'email' => $invitation->email,
            'password' => Hash::make($request->password),
            'role' => $invitation->role,
        ]);

        $invitation->update(['accepted_at' => now()]);

        Auth:login($user);

        return redirect()->route('dashboard')->with('success', 'Registration successful!');
    }

}
