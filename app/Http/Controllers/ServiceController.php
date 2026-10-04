<?php

namespace App\Http\Controllers;

use App\Actions\Services\CreateServiceAction;
use App\Actions\Services\UpdateServiceAction;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');

        $services = Service::query()
            ->when($showArchived, fn (Builder $query) => $query->withTrashed())
            ->orderBy('name')
            ->get();

        return view('services.index', [
            'services' => $services,
            'showArchived' => $showArchived,
        ]);
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(StoreServiceRequest $request, CreateServiceAction $action): RedirectResponse
    {
        $action->execute($request->toDTO());

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        return view('services.edit', ['service' => $service]);
    }

    public function update(UpdateServiceRequest $request, UpdateServiceAction $action, Service $service): RedirectResponse
    {
        $action->execute($request->toDTO(), $service);

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        if ($service->hasActiveSchedules()) {
            return redirect()->route('services.index')
                ->with('error', "\"{$service->name}\" is used by active schedules. Switch those schedules off before you archive it.");
        }

        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service archived.');
    }

    public function restore(Service $service): RedirectResponse
    {
        $service->restore();

        return redirect()->route('services.index', ['archived' => 1])->with('success', 'Service restored.');
    }
}
