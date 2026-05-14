<x-layouts.app>
    <div class="max-w-2xl mx-auto py-10 px-4">

        <div class="mb-6">
            <a href="{{ route('customers.index') }}"
               class="text-sm text-slate-500 hover:text-slate-700 transition-colors duration-150 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Customers
            </a>
        </div>

        <div class="bg-white rounded-md border border-slate-200 overflow-hidden mb-4">

            <div class="bg-slate-50 border-b border-slate-200 px-6 py-5">
                <h1 class="text-lg font-semibold text-slate-800">Create Customer</h1>
                <p class="text-sm text-slate-500">Add a new customer to the system</p>
            </div>

            <form method="POST" action="{{ route('customers.store') }}">
                @csrf

                <div class="divide-y divide-slate-100">

                    <div class="px-6 py-4">
                        <label for="name" class="block text-sm font-medium text-slate-500 mb-1">Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Full name"
                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('name') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                        />
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="px-6 py-4">
                        <label for="phone" class="block text-sm font-medium text-slate-500 mb-1">Phone</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="07700 900000"
                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('phone') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                        />
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="px-6 py-4">
                        <label for="email" class="block text-sm font-medium text-slate-500 mb-1">
                            Email
                            <span class="text-slate-400 font-normal">(optional)</span>
                        </label>
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

                    <div class="px-6 py-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                name="invite_to_portal"
                                value="1"
                                {{ old('invite_to_portal') ? 'checked' : '' }}
                                class="rounded border-slate-300 text-sky-500 focus:ring-sky-500"
                            />
                            <span class="text-sm font-medium text-slate-700">Send portal invite</span>
                        </label>
                        <p class="mt-1 text-xs text-slate-400 ml-7">Requires an email address. Sends a link for the customer to set their password.</p>
                    </div>

                </div>

                <div class="flex items-center justify-end gap-2 px-6 py-4 bg-slate-50 border-t border-slate-200">
                    <button
                        type="submit"
                        class="bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium px-4 py-2 rounded-md transition-colors duration-150"
                    >
                        Create Customer
                    </button>
                    <a href="{{ route('customers.index') }}"
                       class="bg-white hover:bg-slate-100 text-slate-700 text-sm font-medium px-4 py-2 rounded-md border border-slate-200 transition-colors duration-150">
                        Cancel
                    </a>
                </div>

            </form>
        </div>

    </div>
</x-layouts.app>
