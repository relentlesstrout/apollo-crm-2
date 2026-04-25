<?php

namespace App\Http\Controllers;

use App\Actions\Invitations\InviteUserAction;
use App\Http\Requests\InviteRequest;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    public function show()
    {
        return view('admin.invite');
    }

    public function store(InviteRequest $request, InviteUserAction $action)
    {
        $action->execute($request->email, $request->role, $request->user());
    }
}
