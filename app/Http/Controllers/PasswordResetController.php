<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordResetRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function show($token)
    {
        $email = request()->query('email');

        return view('auth.reset-password', compact(['token', 'email']));
    }

    public function store(PasswordResetRequest $request)
    {

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
            event(new PasswordReset($user));
        });

        return $status === Password::PASSWORD_RESET
             ? redirect()->route('login')->with('success', __($status))
             : back()->withErrors(['email' => [__($status)]]);
    }
}
