<x-layouts.login>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-md p-6 bg-white rounded-2xl shadow">
            <div class="flex justify-center mb-4">
                <x-logo class=""/>
            </div>

            <div>
                <h1 class="text-xl font-semibold text-slate-800 text-center mb-4">Password Reset</h1>
                <p class="text-m text-slate-500 text-center">Reset your Apollo Window Cleaning Portal Password</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <div class="mb-4">

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="px-6 py-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" type="password"
                               name="password"
                               class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                               @error('password')
                               border-red-300 focus:ring-red-500 focus:border-red-500
                               @enderror"
                               placeholder="Enter your password">
                        @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="px-6 py-2">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input id="password_confirmation" type="password"
                               name="password_confirmation"
                               class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                               @error('password_confirmation') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                               placeholder="Confirm your password">
                        @error('password_confirmation')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-blue-500 text-white py-2 mt-4 rounded-md hover:bg-blue-600">Set Password</button>

                </div>
            </form>
        </div>

    </div>

</x-layouts.login>
