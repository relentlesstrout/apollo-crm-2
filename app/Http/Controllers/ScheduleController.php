<?php

namespace App\Http\Controllers;

use App\Actions\Schedules\CreateScheduleAction;
use App\Actions\Schedules\UpdateScheduleAction;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Property;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;

class ScheduleController extends Controller
{
    public function create(Property $property)
    {
        return view('schedules.create', [
            'property' => $property,
            'services' => Service::whereIn('id', $property->propertyServices()->pluck('service_id'))->orderBy('name')->get(),
        ]);
    }

    public function store(Property $property, StoreScheduleRequest $request, CreateScheduleAction $action): RedirectResponse
    {
        $action->execute($property, $request->toDTO());

        return redirect()->route('properties.show', $property)->with('success', 'Schedule added.');
    }

    public function edit(Schedule $schedule)
    {
        return view('schedules.edit', [
            'schedule' => $schedule,
            'services' => Service::whereIn('id', $schedule->property->propertyServices()->pluck('service_id'))->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateScheduleRequest $request, UpdateScheduleAction $action, Schedule $schedule): RedirectResponse
    {
        $action->execute($request->toDTO($schedule), $schedule);

        return redirect()->route('properties.show', $schedule->property)->with('success', 'Schedule updated.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $property = $schedule->property;
        $schedule->delete();

        return redirect()->route('properties.show', $property)->with('success', 'Schedule removed.');
    }
}
