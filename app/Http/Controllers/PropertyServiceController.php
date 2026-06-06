<?php

namespace App\Http\Controllers;

use App\Actions\PropertyServices\CreatePropertyServiceAction;
use App\Actions\PropertyServices\UpdatePropertyServiceAction;
use App\Http\Requests\StorePropertyServiceRequest;
use App\Http\Requests\UpdatePropertyServiceRequest;
use App\Models\Property;
use App\Models\PropertyService;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;

class PropertyServiceController extends Controller
{
    public function create(Property $property)
    {
        return view('property-services.create', [
            'property' => $property,
            'services' => Service::orderBy('name')->get(),
        ]);
    }

    public function store(Property $property, StorePropertyServiceRequest $request, CreatePropertyServiceAction $action): RedirectResponse
    {
        $action->execute($property, $request->toDTO());

        return redirect()->route('properties.show', $property)->with('success', 'Service added to property.');
    }

    public function edit(PropertyService $propertyService)
    {
        return view('property-services.edit', [
            'propertyService' => $propertyService,
            'services' => Service::orderBy('name')->get(),
        ]);
    }

    public function update(UpdatePropertyServiceRequest $request, UpdatePropertyServiceAction $action, PropertyService $propertyService): RedirectResponse
    {
        $action->execute($request->toDTO(), $propertyService);

        return redirect()->route('properties.show', $propertyService->property)->with('success', 'Service updated.');
    }

    public function destroy(PropertyService $propertyService): RedirectResponse
    {
        $property = $propertyService->property;
        $propertyService->delete();

        return redirect()->route('properties.show', $property)->with('success', 'Service removed.');
    }
}
