<?php

namespace App\Http\Controllers;

use App\Actions\Services\ArchiveServiceAction;
use App\Actions\Services\CreateServiceAction;
use App\Actions\Services\RestoreServiceAction;
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

    public function destroy(Service $service, ArchiveServiceAction $action): RedirectResponse
    {
        $result = $action->execute($service);

        return redirect()->route('services.index')->with('success', sprintf(
            '"%s" archived. %d price(s) archived, %d schedule(s) switched off, %d open job(s) updated, %d open job(s) cancelled.',
            $service->name,
            $result['prices'],
            $result['schedules'],
            $result['jobsUpdated'],
            $result['jobsCancelled'],
        ));
    }

    public function restore(Service $service, RestoreServiceAction $action): RedirectResponse
    {
        $prices = $action->execute($service);

        return redirect()->route('services.index', ['archived' => 1])->with('success', sprintf(
            '"%s" restored with %d price(s). Its schedules are still switched off: switch them on again on each property.',
            $service->name,
            $prices,
        ));
    }
}
