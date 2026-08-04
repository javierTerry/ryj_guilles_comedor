<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 100 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{ __('Asignación de Roles a Usuarios Registrados') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Gestión individual de nivel de acceso para cada usuario del sistema</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                    Rol: Super Admin (1)
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Pestañas de Navegación de Submenús -->
            <div class="flex border-b border-gray-200 bg-white px-4 pt-3 rounded-t-xl shadow-2xs">
                <a href="{{ route('admin.menu-roles.menus') }}" class="px-5 py-3 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 flex items-center gap-2 border-transparent transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Asignación de Visibilidad de Menús y Submenús por Rol
                </a>
                <a href="{{ route('admin.menu-roles.users') }}" class="px-5 py-3 border-b-2 font-semibold text-sm flex items-center gap-2 border-indigo-600 text-indigo-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 100 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Asignación de Roles a Usuarios Registrados
                </a>
            </div>

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

            <!-- Sección de Gestión de Usuarios y Paginación -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Lista de Usuarios Registrados
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Asigna el rol (1: Super Admin, 2: Admin, 3: Usuario) para controlar la visibilidad de los menús</p>
                    </div>

                    <!-- Control de Registros por Página (15, 25, 50, 100) -->
                    <form action="{{ route('admin.menu-roles.users') }}" method="GET" class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-gray-200 shadow-2xs">
                        <label for="per_page" class="text-xs text-gray-500 font-medium whitespace-nowrap">Mostrar:</label>
                        <select id="per_page" name="per_page" onchange="this.form.submit()" class="text-xs font-semibold rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 py-1 px-2.5 bg-gray-50 text-gray-800">
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 registros</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 registros</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 registros</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 registros</option>
                        </select>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-100/70 text-xs uppercase tracking-wider text-gray-500 font-semibold border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3.5">ID</th>
                                <th class="px-6 py-3.5">Nombre Completo</th>
                                <th class="px-6 py-3.5">Correo Electrónico</th>
                                <th class="px-6 py-3.5">Rol Actual</th>
                                <th class="px-6 py-3.5 text-right">Asignar Nuevo Rol</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $u)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-3.5 font-mono text-xs text-gray-400">#{{ $u->id }}</td>
                                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ $u->name }}</td>
                                    <td class="px-6 py-3.5 text-gray-500 font-mono text-xs">{{ $u->email }}</td>
                                    <td class="px-6 py-3.5">
                                        @if($u->role_id === 1)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                                Super Admin (1)
                                            </span>
                                        @elseif($u->role_id === 2)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                Admin (2)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                Usuario (3)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-right">
                                        <form action="{{ route('admin.menu-roles.update-user-role', $u->id) }}" method="POST" class="inline-flex items-center justify-end gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="per_page" value="{{ $perPage }}">
                                            <input type="hidden" name="page" value="{{ $users->currentPage() }}">
                                            <select name="role_id" class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3 font-medium">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}" {{ $u->role_id === $role->id ? 'selected' : '' }}>
                                                        {{ $role->nombre }} (Rol {{ $role->id }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="px-3.5 py-1.5 bg-gray-800 hover:bg-gray-900 text-white text-xs font-medium rounded-lg shadow-sm transition duration-150 flex items-center gap-1">
                                                Actualizar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">
                                        No hay usuarios registrados disponibles.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer con Enlaces de Paginación -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
