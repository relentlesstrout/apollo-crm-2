<nav class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">

            <!-- Left side -->
            <div class="flex h-full items-center gap-8">
                <x-logo/>
                <div class="hidden sm:flex sm:h-full sm:items-stretch sm:gap-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->is('/')">
                        Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('users.index')" :active="request()->is('users*')">
                        Manage Accounts
                    </x-nav-link>
                    <x-nav-link :href="route('customers.index')" :active="request()->is('customers*')">
                        Customers
                    </x-nav-link>
                    <x-nav-link :href="route('properties.index')" :active="request()->is('properties*')">
                        Properties
                    </x-nav-link>
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center gap-4">

                <!-- Profile Button -->
                <a href="#"
                   class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-200 text-slate-700 font-semibold uppercase">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </a>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-slate-600 hover:text-slate-900">
                        Logout
                    </button>
                </form>

            </div>

        </div>
    </div>
</nav>
