<?php

namespace App\Http\Controllers;

use App\Actions\Customers\CreateCustomerAction;
use App\Actions\Customers\GrantCustomerPortalAccessAction;
use App\Actions\Customers\UpdateCustomerAction;
use App\Enums\CustomerStatus;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        return view('customers.index');
    }

    public function create(): View
    {
        return view('customers.create', [
            'statuses' => CustomerStatus::cases(),
        ]);
    }

    public function store(StoreCustomerRequest $request, CreateCustomerAction $action): RedirectResponse
    {
        $customer = $action->execute($request->toDTO());

        return redirect()->route('customers.show', $customer)->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        return view('customers.show', ['customer' => $customer]);
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', [
            'customer' => $customer,
            'statuses' => CustomerStatus::cases(),
        ]);
    }

    public function update(UpdateCustomerRequest $request, UpdateCustomerAction $action, Customer $customer): RedirectResponse
    {
        $action->execute($request->toDTO(), $customer);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated successfully.');
    }

    public function grantPortalAccess(Customer $customer, GrantCustomerPortalAccessAction $action): RedirectResponse
    {
        $action->execute($customer);

        return redirect()->route('customers.edit', $customer)->with('success', 'Portal invite sent.');
    }

    public function resendPortalInvite(Customer $customer): RedirectResponse
    {
        Password::sendResetLink(['email' => $customer->user->email]);

        return redirect()->route('customers.edit', $customer)->with('success', 'Password reset link resent.');
    }
}
