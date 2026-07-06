<?php

namespace App\Http\Controllers;

use App\Actions\CleaningJobs\CreateCleaningJobAction;
use App\Actions\CleaningJobs\UpdateCleaningJobAction;
use App\Actions\CleaningJobs\UpdateCleaningJobStatusAction;
use App\Enums\UserRole;
use App\Http\Requests\StoreCleaningJobRequest;
use App\Http\Requests\UpdateCleaningJobRequest;
use App\Http\Requests\UpdateCleaningJobStatusRequest;
use App\Models\CleaningJob;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;

class CleaningJobController extends Controller
{
    public function index()
    {
        return view('cleaning-jobs.index');
    }

    public function create(Property $property)
    {
        return view('cleaning-jobs.create', [
            'property' => $property,
            'propertyServices' => $property->propertyServices()->with('service')->get(),
            'cleaners' => $this->cleaners(),
        ]);
    }

    public function store(Property $property, StoreCleaningJobRequest $request, CreateCleaningJobAction $action): RedirectResponse
    {
        $job = $action->execute($property, $request->toDTO());

        return redirect()->route('cleaning-jobs.show', $job)->with('success', 'Cleaning job created.');
    }

    public function show(CleaningJob $cleaningJob)
    {
        $cleaningJob->load('property.customer', 'services', 'cleaners');

        return view('cleaning-jobs.show', ['cleaningJob' => $cleaningJob]);
    }

    public function edit(CleaningJob $cleaningJob)
    {
        $cleaningJob->load('services', 'cleaners');

        return view('cleaning-jobs.edit', [
            'cleaningJob' => $cleaningJob,
            'propertyServices' => $cleaningJob->property->propertyServices()->with('service')->get(),
            'cleaners' => $this->cleaners(),
        ]);
    }

    public function update(UpdateCleaningJobRequest $request, UpdateCleaningJobAction $action, CleaningJob $cleaningJob): RedirectResponse
    {
        $action->execute($request->toDTO(), $cleaningJob);

        return redirect()->route('cleaning-jobs.show', $cleaningJob)->with('success', 'Cleaning job updated.');
    }

    public function status(CleaningJob $cleaningJob, UpdateCleaningJobStatusRequest $request, UpdateCleaningJobStatusAction $action): RedirectResponse
    {
        $action->execute($cleaningJob, $request->status());

        return redirect()->route('cleaning-jobs.show', $cleaningJob)->with('success', 'Cleaning job status updated.');
    }

    /**
     * @return Collection<int, User>
     */
    private function cleaners(): Collection
    {
        return User::query()
            ->where('role', UserRole::Cleaner)
            ->orderBy('name')
            ->get();
    }
}
