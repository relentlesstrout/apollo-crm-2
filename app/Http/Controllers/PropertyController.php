<?php

namespace App\Http\Controllers;

use App\Actions\Properties\CreatePropertyAction;
use App\Actions\Properties\UpdatePropertyAction;
use App\Actions\Properties\UpdatePropertyStatusAction;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Requests\UpdatePropertyStatusRequest;
use App\Models\Customer;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;

class PropertyController extends Controller
{
    public function index()
    {
        return view('properties.index');
    }

    public function create(Customer $customer)
    {
        return view('properties.create', ['customer' => $customer]);
    }

    public function store(Customer $customer, StorePropertyRequest $request, CreatePropertyAction $action): RedirectResponse
    {
        $property = $action->execute($customer, $request->toDTO());

        return redirect()->route('properties.show', $property)->with('success', 'Property created added to Customer.');
    }

    public function edit(Property $property)
    {
        return view('properties.edit', ['property' => $property]);
    }

    public function update(UpdatePropertyRequest $request, UpdatePropertyAction $action, Property $property)
    {
        $action->execute($request->toDTO(), $property);

        return redirect()->route('customers.show', $property->customer)->with('success', 'Property updated successfully.');
    }

    public function show(Property $property)
    {
        return view('properties.show', ['property' => $property]);
    }

    public function status(Property $property, UpdatePropertyStatusRequest $request, UpdatePropertyStatusAction $action): RedirectResponse
    {
        $action->execute($property, $request->status());

        return redirect()->route('properties.show', $property)->with('success', 'Property status updated.');
    }
}
