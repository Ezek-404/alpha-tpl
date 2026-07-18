<nav x-data="{ open: false }" class="bg-[#161b22] border-b border-[#30363d]">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo: Inupdate gamit ang alpha custom logo image -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('logo.png') }}" alt="ALPHA Logo" class="h-9 w-auto">
                    </a>
                </div>

                <!-- Navigation Links (Desktop View) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <!-- Dashboard Link -->
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-[#f0f6fc]">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- COC Management Link -->
                    <x-nav-link :href="route('coc.index')" :active="request()->routeIs('coc.index')" class="text-gray-400 hover:text-[#f0f6fc]">
                        {{ __('COC Management') }}
                    </x-nav-link>

                    <!-- CTPL Issuance Link -->
                    <x-nav-link :href="route('ctpl.issuance')" :active="request()->routeIs('ctpl.issuance')" class="text-gray-400 hover:text-[#f0f6fc]">
                        {{ __('CTPL Issuance') }}
                    </x-nav-link>

                    <!-- Transaction Logs Link -->
                    <x-nav-link :href="'#'" :active="false" class="text-gray-400 hover:text-[#f0f6fc]">
                        {{ __('Transaction Logs') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown (Desktop View) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-400 bg-[#161b22] hover:text-[#f0f6fc] focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Button (Mobile Toggle) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-[#f0f6fc] hover:bg-[#21262d] focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile View) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#161b22] border-t border-[#30363d]">
        
        <!-- Idinagdag para sa Mobile View Navigation Links -->
        <div class="pt-2 pb-3 space-y-1 border-b border-[#30363d]">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-300 hover:text-[#f0f6fc]">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('coc.index')" :active="request()->routeIs('coc.index')" class="text-gray-300 hover:text-[#f0f6fc]">
                {{ __('COC Management') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('ctpl.issuance')" :active="request()->routeIs('ctpl.issuance')" class="text-gray-300 hover:text-[#f0f6fc]">
                {{ __('CTPL Issuance') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="'#'" :active="false" class="text-gray-300 hover:text-[#f0f6fc]">
                {{ __('Transaction Logs') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-4">
            <div class="px-4 mb-3">
                <div class="font-bold text-base text-[#f0f6fc]">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-gray-300 hover:text-[#f0f6fc]">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="text-gray-300 hover:text-[#f0f6fc]">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>