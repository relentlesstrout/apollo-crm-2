<?php

namespace App\Http\Controllers;

use App\Actions\Invoices\SendInvoiceAction;
use App\DTOs\Invoice\SendInvoiceData;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class InvoiceController extends Controller
{
    public function store(User $user, SendInvoiceAction $action): RedirectResponse
    {
        abort_unless($user->isCustomer(), 403, 'Invoices can only be sent to customers.');

        $action->execute(new SendInvoiceData(userId: $user->id));

        return redirect()->route('users.show', $user)->with('success', 'Invoice sent successfully.');
    }
}
