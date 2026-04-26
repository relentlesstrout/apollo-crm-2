<nav class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex h-full items-center gap-8">
                <x-logo/>
                <div class="hidden sm:flex sm:h-full sm:items-stretch sm:gap-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->is('/')">
                        Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('users.index')" :active="request()->is('users*')">
                        Manage Accounts
                    </x-nav-link>
                </div>
            </div>
            <div class="flex items-center sm:hidden">
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md p-2 text-slate-400 hover:bg-blue-50 hover:text-slate-600 transition-colors duration-150 focus:outline-2 focus:outline-offset-2 focus:outline-sky-500"
                    aria-controls="mobile-menu"
                    aria-expanded="false"
                    data-mobile-menu-toggle
                >
                    <span class="sr-only">Open main menu</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="h-6 w-6">
                        <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="hidden sm:hidden" id="mobile-menu">
        <div class="space-y-1 border-t border-slate-200 pb-3 pt-2">
            <a href="{{ route('dashboard') }}"
               class="block border-l-4 py-2 pl-3 pr-4 text-sm font-medium transition-colors duration-150
                      {{ request()->is('/') ? 'border-sky-500 bg-blue-50 text-slate-800' : 'border-transparent text-slate-500 hover:border-slate-300 hover:bg-blue-50 hover:text-slate-700' }}">
                Dashboard
            </a>
            <a href="{{ route('users.index') }}"
               class="block border-l-4 py-2 pl-3 pr-4 text-sm font-medium transition-colors duration-150
                      {{ request()->is('users*') ? 'border-sky-500 bg-blue-50 text-slate-800' : 'border-transparent text-slate-500 hover:border-slate-300 hover:bg-blue-50 hover:text-slate-700' }}">
                Manage Accounts
            </a>
        </div>
    </div>
</nav>
