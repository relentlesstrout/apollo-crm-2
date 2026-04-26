<?php

namespace App\Http\Controllers;

use App\Actions\Invitations\InviteUserAction;
use App\Http\Requests\InviteRequest;
use Illuminate\Http\Request;
use App\DTOs\Invitation\InvitationData;

class InviteController extends Controller
{
    public function show()
    {
        return view('admin.invite');
    }

    public function store(InviteRequest $request, InviteUserAction $action)
    {
        $action->execute(InvitationData::fromRequest($request));
    }
}
