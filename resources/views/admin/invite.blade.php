<x-layouts.app>
    <div class="max-w-2xl mx-auto py-10 px-4">

        {{-- Back link --}}
        <div class="mb-6">
            <a href="{{ route('users.index') }}"
               class="text-sm text-slate-500 hover:text-slate-700 transition-colors duration-150 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Users
            </a>
        </div>

        {{-- Main card --}}
        <div class="bg-white rounded-md border border-slate-200 overflow-hidden mb-4">

            {{-- Card header --}}
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-5 flex items-center gap-4">
                <div>
                    <h1 class="text-lg font-semibold text-slate-800">Invite User</h1>
                    <p class="text-sm text-slate-500">Invite a new user to the system via email</p>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('invite.store') }}">
                @csrf
                <div class="divide-y divide-slate-100">

                    {{-- Email --}}
                    <div class="px-6 py-4">
                        <label for="email" class="block text-sm font-medium text-slate-500 mb-1">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="email@example.com"
                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('email') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                        />
                        @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="px-6 py-4">
                        <label for="role" class="block text-sm font-medium text-slate-500 mb-1">
                            Role
                        </label>
                        <select
                            id="role"
                            name="role"
                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                        >
                        @foreach (\App\Enums\UserRole::invitable() as $role)
                            <option value="{{ $role->value }}">{{ $role->label() }}</option>
                        @endforeach
                        </select>
                        @error('role')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-2 px-6 py-4 bg-slate-50 border-t border-slate-200">
                    <button
                        type="submit"
                        class="bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors duration-150"
                    >
                        Create User
                    </button>
                    <a href="{{ route('users.index') }}"
                       class="bg-white hover:bg-slate-100 text-slate-700 text-sm font-medium px-4 py-2 rounded-md border border-slate-200 transition-colors duration-150">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</x-layouts.app>
