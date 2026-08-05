<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    {{ __('Gestión de Roles y Visibilidad de Menús') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Panel de control exclusivo para el Super Administrador (Rol 1)</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                    Rol Actual: Super Admin
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Alert de estatus -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm font-medium text-emerald-800">{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            <!-- Sección 1: Matriz de Menús y Submenús -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            Asignación de Visibilidad de Menús y Submenús por Rol
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Define qué roles pueden visualizar cada ítem de la barra de navegación principal</p>
                    </div>
                    <div class="text-xs text-gray-400 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-2xs">
                        * Nota: El <strong class="text-indigo-600">Super Admin (Rol 1)</strong> visualiza todos los menús por defecto.
                    </div>
                </div>

                <form action="{{ route('admin.menu-roles.update-menus') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-100/70 text-xs uppercase tracking-wider text-gray-500 font-semibold border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3.5">Menú / Submenú</th>
                                    <th class="px-4 py-3.5">Ruta Interna</th>
                                    <th class="px-4 py-3.5 text-center">Super Admin (Rol 1)</th>
                                    <th class="px-4 py-3.5 text-center">Admin (Rol 2)</th>
                                    <th class="px-4 py-3.5 text-center">Usuario (Rol 3)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($menus as $menu)
                                    <!-- Menú Padre -->
                                    <tr class="bg-indigo-50/30 hover:bg-indigo-50/60 transition-colors font-semibold text-gray-900">
                                        <td class="px-6 py-3.5 flex items-center gap-2">
                                            <span class="p-1.5 bg-indigo-100 text-indigo-600 rounded-md">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M3 7l9 6 9-6"/>
                                                </svg>
                                            </span>
                                            <span>{{ $menu->title }}</span>
                                            @if($menu->submenus->count() > 0)
                                                <span class="text-[10px] px-2 py-0.5 bg-gray-200 text-gray-600 rounded-full font-normal">
                                                    {{ $menu->submenus->count() }} submenús
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 text-xs text-gray-500 font-mono">
                                            {{ $menu->route_name ?? '—' }}
                                        </td>
                                        <!-- Super Admin ALWAYS checked -->
                                        <td class="px-4 py-3.5 text-center">
                                            <input type="checkbox" checked disabled class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-not-allowed opacity-75">
                                        </td>
                                        <!-- Admin Rol 2 -->
                                        <td class="px-4 py-3.5 text-center">
                                            <input type="checkbox" 
                                                   name="permissions[{{ $menu->id }}][]" 
                                                   value="2" 
                                                   {{ $menu->roles->contains(2) ? 'checked' : '' }} 
                                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                        </td>
                                        <!-- Usuario Rol 3 -->
                                        <td class="px-4 py-3.5 text-center">
                                            <input type="checkbox" 
                                                   name="permissions[{{ $menu->id }}][]" 
                                                   value="3" 
                                                   {{ $menu->roles->contains(3) ? 'checked' : '' }} 
                                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                        </td>
                                    </tr>

                                    <!-- Submenús Hijos -->
                                    @foreach ($menu->submenus as $submenu)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-2.5 pl-12 flex items-center gap-2 text-gray-700">
                                                <span class="text-gray-300 font-mono">└─</span>
                                                <span class="font-normal">{{ $submenu->title }}</span>
                                            </td>
                                            <td class="px-4 py-2.5 text-xs text-gray-400 font-mono">
                                                {{ $submenu->route_name ?? '—' }}
                                            </td>
                                            <!-- Super Admin ALWAYS checked -->
                                            <td class="px-4 py-2.5 text-center">
                                                <input type="checkbox" checked disabled class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-not-allowed opacity-75">
                                            </td>
                                            <!-- Admin Rol 2 -->
                                            <td class="px-4 py-2.5 text-center">
                                                <input type="checkbox" 
                                                       name="permissions[{{ $submenu->id }}][]" 
                                                       value="2" 
                                                       {{ $submenu->roles->contains(2) ? 'checked' : '' }} 
                                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                            </td>
                                            <!-- Usuario Rol 3 -->
                                            <td class="px-4 py-2.5 text-center">
                                                <input type="checkbox" 
                                                       name="permissions[{{ $submenu->id }}][]" 
                                                       value="3" 
                                                       {{ $submenu->roles->contains(3) ? 'checked' : '' }} 
                                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl shadow-md hover:shadow-lg transition duration-200 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar Cambios de Permisos de Menús
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sección 2: Gestión de Roles de Usuarios -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 100 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Asignación de Roles a Usuarios Registrados
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Asigna el rol (1: Super Admin, 2: Admin, 3: Usuario) a cada cuenta de usuario registrada en la plataforma</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-100/70 text-xs uppercase tracking-wider text-gray-500 font-semibold border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3.5">ID</th>
                                <th class="px-6 py-3.5">Nombre</th>
                                <th class="px-6 py-3.5">Correo Electrónico</th>
                                <th class="px-6 py-3.5">Rol Actual</th>
                                <th class="px-6 py-3.5 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($users as $u)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-3.5 font-mono text-xs text-gray-400">#{{ $u->id }}</td>
                                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ $u->name }}</td>
                                    <td class="px-6 py-3.5 text-gray-500">{{ $u->email }}</td>
                                    <td class="px-6 py-3.5">
                                        @if($u->role_id === 1)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                                Super Admin (1)
                                            </span>
                                        @elseif($u->role_id === 2)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                Admin (2)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                Usuario (3)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-right">
                                        <form action="{{ route('admin.menu-roles.update-user-role', $u->id) }}" method="POST" class="inline-flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role_id" class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 py-1 px-2">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}" {{ $u->role_id === $role->id ? 'selected' : '' }}>
                                                        {{ $role->nombre }} (Rol {{ $role->id }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="px-3 py-1 bg-gray-800 hover:bg-gray-900 text-white text-xs font-medium rounded-lg transition duration-150">
                                                Actualizar Rol
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
