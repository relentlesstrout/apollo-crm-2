<?php

use App\Enums\CustomerStatus;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public string $search = '';

    public string $status = '';

    #[Computed]
    public function customers(): LengthAwarePaginator
    {
       return Customer::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $query) {
                    $query->where('name', 'ilike', '%' . $this->search . '%')
                        ->orWhere('email', 'ilike', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function (Builder $query) {
                $query->where('status', '=', $this->status);
            })
            ->paginate(20);
    }
};
?>

<div>
    <div class="my-4 flex items-center justify-between">
        <div class="flex items-start justify-between">
            <input
                wire:model.live="search"
                placeholder="Search..."
                name="search"
                class="w-50 rounded-md border border-slate-200 px-3 py-2 mr-4 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
            />

            <select name="status" wire:model.live="status" class="rounded-md border border-slate-200 px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                <option value="">All</option>
                <option value="active">Active</option>
                <option value="paused">Paused</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <div>
            <a href="{{route('customers.create')}}" class="bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors duration-150">
                Add new customer
            </a>
        </div>
    </div>

    <div class="rounded-md border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                        Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                        Phone
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @foreach ($this->customers as $customer)
                    <tr wire:key="{{$customer->id}}" class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm text-slate-800 text-left">{{$customer->name}}</td>
                        <td class="px-6 py-4 text-sm text-slate-800 text-left">{{$customer->email}}</td>
                        <td class="px-6 py-4 text-sm text-slate-800 text-left">{{$customer->phone}}</td>
                        <td class="px-6 py-4 text-sm">
                            <span @class([
                                'text-s font-medium px-2.5 py-0.5 rounded-full',
                                'bg-green-50 text-green-700' => $customer->status === CustomerStatus::Active,
                                'bg-amber-50 text-amber-700' => $customer->status === CustomerStatus::Paused,
                                'bg-red-50 text-red-700'    => $customer->status === CustomerStatus::Cancelled,
                            ])>{{ $customer->status->label() }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-center">
                            <div class="flex w-fit mx-auto items-center gap-2">
                                <a
                                    href="{{ route('customers.show', $customer) }}"
                                    class="bg-white hover:bg-slate-100 text-slate-700 text-sm font-medium px-4 py-2 rounded-md border border-slate-200 transition-colors duration-150"
                                >
                                    View
                                </a>
                                <a
                                    href="{{ route('customers.edit', $customer) }}"
                                    class="bg-sky-50 hover:bg-sky-100 text-sky-700  text-sm font-medium px-4 py-2 rounded-md border border-sky-200 transition-colors duration-150"
                                >
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $this->customers->links() }}
    </div>
</div>
