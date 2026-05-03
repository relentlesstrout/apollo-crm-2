<?php

namespace App\Http\Controllers;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\UpdateUserAction;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class UserController extends Controller

{
    public function index()
    {
        $users = User::query()
            ->orderBy('id', 'desc')
            ->paginate(20);

        $props = [];

        return view('users.index', compact('users', 'props'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request, CreateUserAction $action)
    {
        $action->execute($request->toDTO());

        return redirect()->route('users.index');
    }

    public function show(User $user)
    {
        return view('users.show', ['user' => $user]);
    }

    public function edit(User $user)
    {
        return view('users.edit', ['user' => $user]);
    }

    public function update(UpdateUserRequest $request, UpdateUserAction $action, User $user)
    {
        $userDTO = $request->toDTO();

        $action->execute($userDTO, $user);

        return redirect()->route('users.show', $user->id)->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
