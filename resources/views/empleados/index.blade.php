<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between w-full gap-4" x-data="{}">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Empleados') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <!-- Descargar Plantilla -->
                <a
                    href="{{ route('empleados.template') }}"
                    class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150 shadow-sm"
                >
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    {{ __('Plantilla CSV') }}
                </a>

                <!-- Importar CSV -->
                <button
                    @click="$dispatch('open-modal', 'importar-csv')"
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150 shadow"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h10.5A2.25 2.25 0 0020 19.5v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3-3m0 0l3 3m-3-3V15"></path>
                    </svg>
                    {{ __('Importar CSV') }}
                </button>

                <!-- Agregar Empleado (Manual) -->
                <button
                    @click="$dispatch('open-modal', 'crear-empleado')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150 shadow"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Agregar Empleado') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        activeEmpleado: {
            id: null,
            numero_empleado: '',
            nombre: '',
            departamento: '',
            puesto: ''
        },
        openEdit(emp) {
            this.activeEmpleado = {
                id: emp.id,
                numero_empleado: emp.numero_empleado,
                nombre: emp.nombre,
                departamento: emp.departamento || '',
                puesto: emp.puesto || ''
            };
            this.$dispatch('open-modal', 'editar-empleado');
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Success Alerts -->
            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-md shadow-sm transition-all duration-300">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-emerald-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-rose-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-rose-800">Hubo algunos problemas al procesar la solicitud:</h3>
                            <ul class="mt-2 list-disc list-inside text-sm text-rose-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Search and Filter Bar -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <form action="{{ route('empleados.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-end md:gap-4">
                    <!-- Text Search -->
                    <div class="flex-1">
                        <label for="search" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Buscar Empleado</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </span>
                            <input
                                type="text"
                                name="search"
                                id="search"
                                value="{{ request('search') }}"
                                placeholder="Nombre o número de empleado..."
                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm placeholder-gray-400 transition"
                            />
                        </div>
                    </div>

                    <!-- Department Filter -->
                    <div class="w-full md:w-60">
                        <label for="departamento" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Departamento</label>
                        <select
                            name="departamento"
                            id="departamento"
                            class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition"
                        >
                            <option value="">Todos los Departamentos</option>
                            @foreach ($departamentos as $dept)
                                <option value="{{ $dept }}" {{ request('departamento') == $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="w-full md:w-48">
                        <label for="status" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Estado</label>
                        <select
                            name="status"
                            id="status"
                            class="block w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition"
                        >
                            <option value="">Todos los Estados</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 w-full md:w-auto">
                        <button
                            type="submit"
                            class="flex-1 md:flex-none px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition duration-150 ease-in-out focus:ring-2 focus:ring-indigo-500"
                        >
                            Filtrar
                        </button>
                        @if (request()->hasAny(['search', 'departamento', 'status']))
                            <a
                                href="{{ route('empleados.index') }}"
                                class="px-4 py-2.5 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium text-sm rounded-lg text-center transition"
                            >
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Nº Empleado
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Nombre
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Departamento
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Puesto
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Total Visitas
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th scope="col" class="relative px-6 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($empleados as $empleado)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 tracking-wider">
                                        {{ $empleado->numero_empleado }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-950">{{ $empleado->nombre }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $empleado->departamento ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $empleado->puesto ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $empleado->registros_comedor_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if ($empleado->activo)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full"></span>
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                                <span class="w-1.5 h-1.5 mr-1.5 bg-rose-500 rounded-full"></span>
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button
                                            type="button"
                                            @click="openEdit({{ json_encode($empleado) }})"
                                            class="inline-flex items-center text-indigo-600 hover:text-indigo-900 transition"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Editar
                                        </button>

                                        <form action="{{ route('empleados.toggle', $empleado) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="inline-flex items-center {{ $empleado->activo ? 'text-rose-600 hover:text-rose-900' : 'text-emerald-600 hover:text-emerald-900' }} transition"
                                                onclick="return confirm('¿Está seguro de cambiar el estado de este empleado?')"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                                {{ $empleado->activo ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 whitespace-nowrap text-center text-sm text-gray-500">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        No se encontraron empleados registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($empleados->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $empleados->links() }}
                    </div>
                @endif
            </div>

        </div>

    <!-- CREATE MODAL -->
    <x-modal name="crear-empleado" :show="false">
        <form action="{{ route('empleados.store') }}" method="POST" class="p-6">
            @csrf

            <h2 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">
                {{ __('Dar de Alta Empleado') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="new_numero_empleado" :value="__('Número de Empleado (10 dígitos)')" />
                    <x-text-input
                        id="new_numero_empleado"
                        name="numero_empleado"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Escriba exactamente 10 números"
                        maxlength="10"
                        pattern="[0-9]{10}"
                        title="Debe contener exactamente 10 dígitos numéricos"
                        required
                    />
                    <p class="text-xs text-gray-400 mt-1">Solo se permiten números (0-9).</p>
                </div>

                <div>
                    <x-input-label for="new_nombre" :value="__('Nombre Completo')" />
                    <x-text-input
                        id="new_nombre"
                        name="nombre"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Ej. Juan Pérez López"
                        required
                    />
                </div>

                <div>
                    <x-input-label for="new_departamento" :value="__('Departamento')" />
                    <x-text-input
                        id="new_departamento"
                        name="departamento"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Ej. Producción, Logística, etc."
                    />
                </div>

                <div>
                    <x-input-label for="new_puesto" :value="__('Puesto')" />
                    <x-text-input
                        id="new_puesto"
                        name="puesto"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Ej. Operador A, Supervisor, etc."
                    />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                <x-secondary-button @click="$dispatch('close-modal', 'crear-empleado')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                    {{ __('Registrar') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- EDIT MODAL -->
    <x-modal name="editar-empleado" :show="false">
        <form :action="'{{ url('empleados') }}/' + activeEmpleado.id" method="POST" class="p-6">
            @csrf
            @method('PATCH')

            <h2 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">
                {{ __('Modificar Datos del Empleado') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="edit_numero_empleado" :value="__('Número de Empleado (10 dígitos)')" />
                    <x-text-input
                        id="edit_numero_empleado"
                        name="numero_empleado"
                        type="text"
                        class="mt-1 block w-full"
                        x-model="activeEmpleado.numero_empleado"
                        maxlength="10"
                        pattern="[0-9]{10}"
                        title="Debe contener exactamente 10 dígitos numéricos"
                        required
                    />
                </div>

                <div>
                    <x-input-label for="edit_nombre" :value="__('Nombre Completo')" />
                    <x-text-input
                        id="edit_nombre"
                        name="nombre"
                        type="text"
                        class="mt-1 block w-full"
                        x-model="activeEmpleado.nombre"
                        required
                    />
                </div>

                <div>
                    <x-input-label for="edit_departamento" :value="__('Departamento')" />
                    <x-text-input
                        id="edit_departamento"
                        name="departamento"
                        type="text"
                        class="mt-1 block w-full"
                        x-model="activeEmpleado.departamento"
                    />
                </div>

                <div>
                    <x-input-label for="edit_puesto" :value="__('Puesto')" />
                    <x-text-input
                        id="edit_puesto"
                        name="puesto"
                        type="text"
                        class="mt-1 block w-full"
                        x-model="activeEmpleado.puesto"
                    />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                <x-secondary-button @click="$dispatch('close-modal', 'editar-empleado')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                    {{ __('Guardar Cambios') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- IMPORT CSV MODAL -->
    <x-modal name="importar-csv" :show="false">
        <form action="{{ route('empleados.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <h2 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">
                {{ __('Importar Empleados desde CSV') }}
            </h2>

            <div class="space-y-4">
                <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-lg text-xs text-indigo-700 space-y-2">
                    <p class="font-bold uppercase tracking-wider">Instrucciones de Importación:</p>
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Descarga la plantilla CSV con las columnas correspondientes.</li>
                        <li>Completa los datos en las columnas: <code class="bg-indigo-100 px-1 py-0.5 rounded font-mono font-bold">numero_empleado</code>, <code class="bg-indigo-100 px-1 py-0.5 rounded font-mono font-bold">nombre</code>, <code class="bg-indigo-100 px-1 py-0.5 rounded font-mono font-bold">departamento</code>, <code class="bg-indigo-100 px-1 py-0.5 rounded font-mono font-bold">puesto</code>.</li>
                        <li>El <code class="bg-indigo-100 px-1 py-0.5 rounded font-mono font-bold">numero_empleado</code> debe tener exactamente 10 dígitos y ser único.</li>
                        <li>Sube tu archivo y presiona importar. Se reportarán las filas exitosas y los errores si ocurren.</li>
                    </ol>
                </div>

                <div>
                    <x-input-label for="csv_file" :value="__('Seleccionar Archivo CSV')" />
                    <input
                        type="file"
                        name="csv_file"
                        id="csv_file"
                        accept=".csv,text/csv"
                        required
                        class="mt-2 block w-full text-sm text-slate-500
                            file:mr-4 file:py-2.5 file:px-4
                            file:rounded-md file:border-0
                            file:text-xs file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100 file:cursor-pointer"
                    />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                <x-secondary-button @click="$dispatch('close-modal', 'importar-csv')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="bg-emerald-600 hover:bg-emerald-700">
                    {{ __('Importar') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
    </div>
</x-app-layout>
