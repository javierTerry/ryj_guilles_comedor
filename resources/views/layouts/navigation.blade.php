<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="w-full max-w-[95%] lg:max-w-[90%] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-10 w-auto object-contain" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @php
                            $userMenus = \App\Models\Menu::getForUser(Auth::user());
                        @endphp

                        @foreach ($userMenus as $menu)
                            @if ($menu->submenus && $menu->submenus->count() > 0)
                                <!-- Dropdown Menu para ítems con submenús (ej. Reportes) -->
                                @php
                                    $isSubmenuActive = $menu->submenus->contains(function ($sub) {
                                        return $sub->route_name && request()->routeIs($sub->route_name);
                                    });
                                @endphp
                                <div class="hidden sm:flex sm:items-center sm:ms-0">
                                    <x-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button class="inline-flex items-center px-1 pt-1 border-b-2 {{ $isSubmenuActive ? 'border-indigo-400 text-gray-900 focus:border-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                                <div>{{ __($menu->title) }}</div>
                                                <div class="ms-1">
                                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </button>
                                        </x-slot>

                                        <x-slot name="content">
                                            @foreach ($menu->submenus as $submenu)
                                                @if ($submenu->route_name && Route::has($submenu->route_name))
                                                    <x-dropdown-link :href="route($submenu->route_name)" :active="request()->routeIs($submenu->route_name)">
                                                        {{ __($submenu->title) }}
                                                    </x-dropdown-link>
                                                @endif
                                            @endforeach
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            @elseif ($menu->route_name && Route::has($menu->route_name))
                                <x-nav-link :href="route($menu->route_name)" :active="request()->routeIs($menu->route_name)">
                                    {{ __($menu->title) }}
                                </x-nav-link>
                            @endif
                        @endforeach
                    @else
                        <x-nav-link :href="route('reservaciones.create')" :active="request()->routeIs('reservaciones.create')">
                            {{ __('Reservar') }}
                        </x-nav-link>
                        <x-nav-link :href="route('reservaciones.cancel_view')" :active="request()->routeIs('reservaciones.cancel_view')">
                            {{ __('Cancelar') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            @auth
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center gap-2">
                                <span>{{ Auth::user()->name }}</span>
                                @if(Auth::user()->isSuperAdmin())
                                    <span class="text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded font-bold">SUPER ADMIN</span>
                                @elseif(Auth::user()->isAdmin())
                                    <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-bold">ADMIN</span>
                                @endif
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        @if(Auth::user()->isSuperAdmin())
                            <x-dropdown-link :href="route('admin.menu-roles.index')" :active="request()->routeIs('admin.menu-roles.*')">
                                {{ __('Gestión de Roles y Menús') }}
                            </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Cerrar Sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @else
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 transition">Iniciar Sesión</a>
            </div>
            @endauth

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @php
                    $responsiveUserMenus = \App\Models\Menu::getForUser(Auth::user());
                @endphp

                @foreach ($responsiveUserMenus as $menu)
                    @if ($menu->submenus && $menu->submenus->count() > 0)
                        <div class="pt-2 pb-1 border-t border-gray-100">
                            <div class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __($menu->title) }}</div>
                            @foreach ($menu->submenus as $submenu)
                                @if ($submenu->route_name && Route::has($submenu->route_name))
                                    <x-responsive-nav-link :href="route($submenu->route_name)" :active="request()->routeIs($submenu->route_name)">
                                        {{ __($submenu->title) }}
                                    </x-responsive-nav-link>
                                @endif
                            @endforeach
                        </div>
                    @elseif ($menu->route_name && Route::has($menu->route_name))
                        <x-responsive-nav-link :href="route($menu->route_name)" :active="request()->routeIs($menu->route_name)">
                            {{ __($menu->title) }}
                        </x-responsive-nav-link>
                    @endif
                @endforeach
            @else
                <x-responsive-nav-link :href="route('reservaciones.create')" :active="request()->routeIs('reservaciones.create')">
                    {{ __('Reservar') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reservaciones.cancel_view')" :active="request()->routeIs('reservaciones.cancel_view')">
                    {{ __('Cancelar') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 flex items-center gap-2">
                    <span>{{ Auth::user()->name }}</span>
                    @if(Auth::user()->isSuperAdmin())
                        <span class="text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded font-bold">SUPER ADMIN</span>
                    @endif
                </div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                @if(Auth::user()->isSuperAdmin())
                    <x-responsive-nav-link :href="route('admin.menu-roles.index')" :active="request()->routeIs('admin.menu-roles.*')">
                        {{ __('Gestión de Roles y Menús') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 transition">Iniciar Sesión</a>
            </div>
        </div>
        @endauth
    </div>
</nav>
