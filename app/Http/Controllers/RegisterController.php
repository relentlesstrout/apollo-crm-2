<?php

namespace App\Http\Controllers;

use App\Actions\Invitations\CheckValidRegistrationAction;
use App\Actions\Invitations\RegisterUserAction;
use App\Actions\Users\CreateUserAction;
use App\Enums\UserRole;
use App\Http\Requests\RegisterRequest;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function show (string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        return view('auth.register', compact('invitation'));
    }

    public function store (RegisterRequest $request, RegisterUserAction $action, string $token)
    {
        try {
            $invitation = CheckValidRegistrationAction::execute($request, $token);
            $user = $action->execute($request->toDTO());
            $invitation->update(['accepted_at' => now()]);
            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Registration successful!');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }

}
