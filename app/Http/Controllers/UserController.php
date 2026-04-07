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
        $users = User::withTrashed()
            ->orderBy('id', 'desc')
            ->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request, CreateUserAction $action)
    {
        $userDTO = $request->toDTO();
        $action->execute($userDTO);

        redirect()->route('users.index');
    }

    public function show($id)
    {
        return view('users.show', $id);
    }

    public function edit($id)
    {
        return view('users.edit', $id);
    }

    public function update(UpdateUserRequest $request, UpdateUserAction $action, User $user)
    {
        $userDTO = $request->toDTO();

        $action->execute($userDTO, $user);

        redirect()->route('users.show', $user->id)->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
