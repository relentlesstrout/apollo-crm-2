<x-layouts.login>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-md p-6 bg-white rounded-2xl shadow">
            <div class="flex justify-center mb-4">
                <x-logo class=""/>
            </div>
            <div>
                <h1 class="text-xl font-semibold text-slate-800 text-center">Create Account</h1>
                <p class="text-m text-slate-500 text-center">You've been invited to join Apollo</p>
            </div>

            <form method="POST" action="{{ route('register.store', $invitation->token) }}">
                @csrf

                {{-- Invitation token --}}
                <input type="hidden" name="token" value="{{ $invitation->token }}">

                <div class="mb-4">

                    {{-- Prefilled from invitation --}}
                    <div class="px-6 py-2 mt-2">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="flex items-center gap-2 mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                            <svg class="h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span class="text-sm text-slate-500">{{ $invitation->email }}</span>
                        </div>
                        <input type="hidden" name="email" value="{{ $invitation->email }}">
                    </div>

                    <div class="px-6 py-2">
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <div class="flex items-center gap-2 mt-1 w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                            <svg class="h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span class="text-sm text-slate-500 capitalize">{{ $invitation->role }}</span>
                        </div>
                        <input type="hidden" name="role" value="{{ $invitation->role }}">
                    </div>

                    {{-- User-filled fields --}}
                    <div class="px-6 py-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('name') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Enter your full name">
                        @error('name')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="px-6 py-2">
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('phone') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Enter your phone number">
                        @error('phone')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="px-6 py-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('password') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Create a password">
                        @error('password')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="px-6 py-2">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                            placeholder="Confirm your password">
                    </div>

                    <div class="px-6">
                        <button type="submit" class="w-full bg-blue-500 text-white py-2 mt-4 rounded-md hover:bg-blue-600">
                            Create Account
                        </button>
                    </div>
                    @error('role')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                @foreach ($errors->all() as $error)
                    <span class="text-red-500 text-xs mt-1">{{ $error }}</span>
                @endforeach
            </form>
        </div>
    </div>
</x-layouts.login>
