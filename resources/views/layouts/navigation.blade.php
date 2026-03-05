<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-2 text-indigo-600 font-bold text-lg">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                {{ __('Ship Order') }}
                            </a>
                        @elseif(auth()->user()->is_warehouse)
                            <a href="{{ route('warehouse.index') }}" class="flex items-center gap-2 text-indigo-600 font-bold text-lg">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                {{ __('Ship Order') }}
                            </a>
                        @else
                            <a href="{{ route('ships.index') }}" class="flex items-center gap-2 text-indigo-600 font-bold text-lg">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                {{ __('Ship Order') }}
                            </a>
                        @endif
                    @else
                        <span class="flex items-center gap-2 text-indigo-600 font-bold text-lg">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            {{ __('Ship Order') }}
                        </span>
                    @endauth
                </div>

                <!-- Navigation Links -->
                @auth
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(auth()->user()->is_admin)
                        <x-nav-link :href="route('admin.export.index')" :active="request()->routeIs('admin.export.*')">
                            {{ __('Export') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                            {{ __('Orders') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.vendors.index')" :active="request()->routeIs('admin.vendors.*')">
                            {{ __('Vendors') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                            {{ __('Products') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Users') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.admins.index')" :active="request()->routeIs('admin.admins.*')">
                            {{ __('Admins') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.warehouses.index')" :active="request()->routeIs('admin.warehouses.*')">
                            {{ __('Warehouses') }}
                        </x-nav-link>
                    @elseif(auth()->user()->is_warehouse)
                        <x-nav-link :href="route('warehouse.index')" :active="request()->routeIs('warehouse.*')">
                            {{ __('Gudang') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('ships.index')" :active="request()->routeIs('ships.*')">
                            {{ __('My Ships') }}
                        </x-nav-link>
                        <x-nav-link :href="route('orders.create')" :active="request()->routeIs('orders.create')">
                            {{ __('New Order') }}
                        </x-nav-link>
                        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.index') || request()->routeIs('orders.show')">
                            {{ __('My Orders') }}
                        </x-nav-link>
                    @endif
                </div>
                @endauth
            </div>

            <!-- Settings Dropdown -->
            @auth
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
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
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @endauth
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    @auth
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(auth()->user()->is_admin)
                <x-responsive-nav-link :href="route('admin.export.index')" :active="request()->routeIs('admin.export.*')">{{ __('Export') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">{{ __('Orders') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.vendors.index')" :active="request()->routeIs('admin.vendors.*')">{{ __('Vendors') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">{{ __('Products') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">{{ __('Users') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.admins.index')" :active="request()->routeIs('admin.admins.*')">{{ __('Admins') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.warehouses.index')" :active="request()->routeIs('admin.warehouses.*')">{{ __('Warehouses') }}</x-responsive-nav-link>
            @elseif(auth()->user()->is_warehouse)
                <x-responsive-nav-link :href="route('warehouse.index')" :active="request()->routeIs('warehouse.*')">{{ __('Gudang') }}</x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('ships.index')" :active="request()->routeIs('ships.*')">{{ __('My Ships') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('orders.create')" :active="request()->routeIs('orders.create')">{{ __('New Order') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.index')">{{ __('My Orders') }}</x-responsive-nav-link>
            @endif
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
    @endauth
</nav>
