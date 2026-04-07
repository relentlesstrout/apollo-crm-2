<?php

namespace App\Http\Controllers;

use App\Actions\Users\CreateUserAction;
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

    public function store(Request $request, CreateUserAction $action)
    {
        $user = $action->execute($request->validated);

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

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->validated());

        redirect()->route('users.index');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
