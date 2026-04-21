<x-layouts.app>
    <x-title
        title="View {{ $user->name }}"
        description="View {{ $user->name }}'s details">
    </x-title>
    <div class="max-w-2xl mx-auto py-10 px-4">
        <div class="mb-6">
            <a href="{{ route('users.index') }}"
               class="text-sm text-slate-500 hover:text-slate-700 transition-colors duration-150 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Users
            </a>
        </div>

        <div class="bg-white rounded-md border border-slate-200 overflow-hidden mb-4">

            <div class="bg-slate-50 border-b border-slate-200 px-6 py-5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div>
                        <h1 class="text-lg font-semibold text-slate-800">{{ $user->name }}</h1>
                        <p class="text-sm text-slate-500">{{ $user->email }}</p>
                    </div>
                </div>

                @php
                    $roleColours = [
                        'admin'    => 'bg-sky-100 text-sky-700 border border-sky-200',
                        'cleaner'  => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                        'customer' => 'bg-slate-100 text-slate-600 border border-slate-200',
                    ];
                    $roleBadge = $roleColours[$user->role->value] ?? $roleColours['customer'];
                @endphp

                <span class="text-xs font-medium px-3 py-1 rounded-full capitalize {{ $roleBadge }}">
                    {{ $user->role->value }}
                </span>
            </div>

            <dl class="divide-y divide-slate-100">

                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <dt class="text-sm font-medium text-slate-500 w-32 shrink-0">Name</dt>
                    <dd class="text-sm text-slate-800 text-right">{{ $user->name }}</dd>
                </div>

                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <dt class="text-sm font-medium text-slate-500 w-32 shrink-0">Email</dt>
                    <dd class="text-sm text-slate-800 text-right">{{ $user->email }}</dd>
                </div>

                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <dt class="text-sm font-medium text-slate-500 w-32 shrink-0">Phone</dt>
                    <dd class="text-sm text-slate-800 text-right">{{ $user->phone ?? '—' }}</dd>
                </div>


                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <dt class="text-sm font-medium text-slate-500 w-32 shrink-0">Role</dt>
                    <dd class="text-sm text-right">
                        <span class="text-xs font-medium px-3 py-1 rounded-full capitalize {{ $roleBadge }}">
                            {{ $user->role->value }}
                        </span>
                    </dd>
                </div>

                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <dt class="text-sm font-medium text-slate-500 w-32 shrink-0">Created</dt>
                    <dd class="text-sm text-slate-800 text-right">{{ $user->created_at->format('j F Y') }}</dd>
                </div>

                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <dt class="text-sm font-medium text-slate-500 w-32 shrink-0">Updated</dt>
                    <dd class="text-sm text-slate-800 text-right">{{ $user->updated_at->format('j F Y') }}</dd>
                </div>

            </dl>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('users.edit', $user) }}"
               class="bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors duration-150">
                Edit
            </a>
            <form method="POST"
                  action="{{ route('users.destroy', $user) }}"
                  onsubmit="return confirm('Are you sure you want to delete {{ addslashes($user->name) }}?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-white hover:bg-red-100 text-red-600 text-sm font-medium px-4 py-2 rounded-md border border-red-200 transition-colors duration-150">
                    Delete
                </button>
            </form>
            <a href="{{ route('users.index') }}"
               class="bg-white hover:bg-slate-100 text-slate-700 text-sm font-medium px-4 py-2 rounded-md border border-slate-200 transition-colors duration-150">
                Cancel
            </a>
        </div>
    </div>
</x-layouts.app>

