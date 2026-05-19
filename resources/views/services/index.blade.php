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

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

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
                            <tr class="hover:bg-slate-50 transition-colors duration-100">
                                <td class="px-6 py-4 font-medium text-slate-800">{{ $service->name }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ $service->description ?? '—' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('services.edit', $service) }}"
                                           class="text-sky-600 hover:text-sky-800 font-medium">Edit</a>
                                        <form method="POST" action="{{ route('services.destroy', $service) }}"
                                              onsubmit="return confirm('Delete this service?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium">
                                                Delete
                                            </button>
                                        </form>
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
