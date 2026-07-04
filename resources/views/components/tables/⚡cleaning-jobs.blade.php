<?php

use App\Enums\CleaningJobStatus;
use App\Models\CleaningJob;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $search = '';

    public string $status = '';

    #[Computed]
    public function jobs(): LengthAwarePaginator
    {
        return CleaningJob::query()
            ->with('property')
            ->when($this->search, function (Builder $query) {
                $terms = array_filter(explode(' ', trim($this->search)));

                foreach ($terms as $term) {
                    $query->whereHas('property', function (Builder $query) use ($term) {
                        $query->where('house', 'ilike', '%' . $term . '%')
                            ->orWhere('street', 'ilike', '%' . $term . '%');
                    });
                }
            })
            ->when($this->status, function (Builder $query) {
                $query->where('status', '=', $this->status);
            })
            ->orderByDesc('scheduled_at')
            ->paginate(20);
    }
}
?>

<div>
    <div class="my-4 flex items-center justify-between">
        <div class="flex items-start justify-between">
            <input
                wire:model.live="search"
                placeholder="Search by address..."
                name="search"
                class="w-50 rounded-md border border-slate-200 px-3 py-2 mr-4 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
            />

            <select name="status" wire:model.live="status"
                    class="rounded-md border border-slate-200 px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                <option value="">All statuses</option>
                @foreach (CleaningJobStatus::cases() as $case)
                    <option value="{{ $case->value }}">{{ $case->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="rounded-md border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Property</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Scheduled</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @foreach ($this->jobs as $job)
                    <tr wire:key="{{ $job->id }}" class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm text-slate-800 text-left">{{ $job->property->house }}, {{ $job->property->street }}</td>
                        <td class="px-6 py-4 text-sm text-slate-800 text-left">{{ $job->scheduled_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span @class([
                                'text-s font-medium px-2.5 py-0.5 rounded-full',
                                'bg-sky-50 text-sky-700'         => $job->status === CleaningJobStatus::Scheduled,
                                'bg-amber-50 text-amber-700'     => $job->status === CleaningJobStatus::InProgress,
                                'bg-green-50 text-green-700'     => $job->status === CleaningJobStatus::Completed,
                                'bg-red-50 text-red-700'         => $job->status === CleaningJobStatus::Cancelled,
                            ])>{{ $job->status->label() }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-center">
                            <div class="flex w-fit mx-auto items-center gap-2">
                                <a
                                    href="{{ route('cleaning-jobs.show', $job) }}"
                                    class="bg-white hover:bg-slate-100 text-slate-700 text-sm font-medium px-4 py-2 rounded-md border border-slate-200 transition-colors duration-150"
                                >
                                    View
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $this->jobs->links() }}
    </div>
</div>
