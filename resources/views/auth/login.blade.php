<x-layouts.login>

    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-md p-6 bg-white rounded-2xl shadow">

            <div class="flex justify-center mb-4">
                <x-logo class=""/>
            </div>

            <div>
                <h1 class="text-xl font-semibold text-slate-800 text-center">Sign In</h1>
                <p class="text-m text-slate-500 text-center">Sign into your Apollo Account</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">

                    <div class="px-6 py-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            type="email" id="email"
                            name="email"
                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('email') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Enter your email">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="px-6 py-2">
                        <label for="password" class="block text-sm font-medium text-gray-700 mt-4">Password</label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('password') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Enter your password">
                        @error('password')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="px-6 py-2 mt-2">
                        <label for="remember" class="flex items-center gap-2 text-sm text-slate-700">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember"
                                value="1"
                                class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500"
                            >
                            Remember me
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-blue-500 text-white py-2 mt-4 rounded-md hover:bg-blue-600">Sign In</button>

                </div>
            </form>
        </div>
    </div>

</x-layouts.login>


