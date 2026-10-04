<x-layouts.app>
    <div class="max-w-4xl mx-auto py-10 px-4">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Services</h1>
                <p class="text-sm text-slate-500 mt-1">Manage the services your business offers.</p>
            </div>
            <a href="{{ route('services.create') }}"
               class="bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors duration-150">
                Add Service
            </a>
        </div>

        <div class="flex justify-end mb-3">
            <a href="{{ route('services.index', $showArchived ? [] : ['archived' => 1]) }}"
               class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900"
               role="switch" aria-checked="{{ $showArchived ? 'true' : 'false' }}">
                <span class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-150 {{ $showArchived ? 'bg-sky-500' : 'bg-slate-300' }}">
                    <span class="inline-block h-4 w-4 rounded-full bg-white shadow transition-transform duration-150 {{ $showArchived ? 'translate-x-4' : 'translate-x-0.5' }}"></span>
                </span>
                Show archived services
            </a>
        </div>

        <div class="bg-white rounded-md border border-slate-200 overflow-hidden">
            @if ($services->isEmpty())
                <div class="px-6 py-10 text-center text-sm text-slate-500">
                    No services yet. <a href="{{ route('services.create') }}" class="text-sky-600 hover:underline">Add one.</a>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($services as $service)
                            <tr class="hover:bg-slate-50 transition-colors duration-100 {{ $service->trashed() ? 'bg-slate-50/60' : '' }}">
                                <td class="px-6 py-4 font-medium {{ $service->trashed() ? 'text-slate-400' : 'text-slate-800' }}">
                                    {{ $service->name }}
                                    @if ($service->trashed())
                                        <span class="ml-2 text-xs font-medium px-2 py-0.5 rounded-full bg-slate-200 text-slate-600">Archived</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500">{{ $service->description ?? '—' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        @if ($service->trashed())
                                            <form method="POST" action="{{ route('services.restore', $service) }}">
                                                @csrf
                                                <button type="submit" class="text-sky-600 hover:text-sky-800 font-medium">
                                                    Restore
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('services.edit', $service) }}"
                                               class="text-sky-600 hover:text-sky-800 font-medium">Edit</a>
                                            <form method="POST" action="{{ route('services.destroy', $service) }}"
                                                  onsubmit="return confirm('Archive this service? It will no longer be offered for new prices or schedules. Past jobs keep it.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium">
                                                    Archive
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</x-layouts.app>
