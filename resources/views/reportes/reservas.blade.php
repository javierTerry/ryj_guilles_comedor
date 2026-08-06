<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Reporte de Reservaciones por Día') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Consulta detallada de reservaciones de comedor por colaborador y horario, filtrada por el día actual por defecto.
                </p>
            </div>
            <a href="{{ route('reportes.reservas_export', request()->query()) }}"
                class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-sm transition duration-150 gap-2"
                title="Descargar reporte de reservaciones en formato CSV para Excel">
                <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar Reservas CSV
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="w-full max-w-[95%] lg:max-w-[90%] mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- TARJETAS DE RESUMEN -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Reservaciones Registradas</p>
                        <h3 class="text-3xl font-extrabold text-indigo-600 mt-1">
                            {{ number_format($totalReservas) }}
                        </h3>
                    </div>
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Rango de Consulta</p>
                        <h3 class="text-lg font-bold text-amber-600 mt-1">
                            Del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            @if(!$hasCustomDateFilter)
                                <span class="text-indigo-600 font-semibold">📅 Día Actual (Por Defecto)</span>
                            @else
                                Personalizado por filtro
                            @endif
                        </p>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Paginación / Formato</p>
                        <h3 class="text-xl font-bold text-slate-700 mt-1">{{ $perPage }} <span class="text-xs font-semibold text-gray-400">reservas/pág</span></h3>
                        <p class="text-xs text-emerald-600 font-bold mt-0.5">CSV (UTF-8 Excel)</p>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- PANEL DE FILTROS Y RANGO DE FECHAS -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filtros de Búsqueda y Rango de Fechas
                </h3>

                <form method="GET" action="{{ route('reportes.reservas') }}" class="space-y-4 w-full">

                    <!-- PRIMERA FILA: BÚSQUEDA, DEPARTAMENTO, ESTATUS Y HORARIO -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full pt-3 border-t border-gray-100">
                        <!-- 1. BÚSQUEDA POR NOMBRE / NÚMERO -->
                        <div>
                            <label for="search" class="block text-xs font-semibold text-gray-600 mb-1.5">Nombre o Nº Empleado</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Ej: 1024 o Juan"
                                class="w-full h-10 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <!-- 2. FILTRO DEPARTAMENTO -->
                        <div>
                            <label for="departamento" class="block text-xs font-semibold text-gray-600 mb-1.5">Departamento</label>
                            <select name="departamento" id="departamento"
                                class="w-full h-10 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos los departamentos</option>
                                @foreach ($departamentos as $dept)
                                    <option value="{{ $dept }}" {{ request('departamento') == $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. FILTRO ESTATUS -->
                        <div>
                            <label for="estatus" class="block text-xs font-semibold text-gray-600 mb-1.5">Estatus Empleado</label>
                            <select name="estatus" id="estatus"
                                class="w-full h-10 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos los estatus</option>
                                <option value="1" {{ request('estatus') === '1' ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ request('estatus') === '0' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>

                        <!-- 4. FILTRO HORARIO RESERVADO -->
                        <div>
                            <label for="hora" class="block text-xs font-semibold text-gray-600 mb-1.5">Horario Reservado</label>
                            <select name="hora" id="hora"
                                class="w-full h-10 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos los horarios</option>
                                <option value="12:30" {{ request('hora') === '12:30' ? 'selected' : '' }}>12:30 p.m.</option>
                                <option value="13:15" {{ request('hora') === '13:15' ? 'selected' : '' }}>1:15 p.m.</option>
                                <option value="14:00" {{ request('hora') === '14:00' ? 'selected' : '' }}>2:00 p.m.</option>
                                <option value="14:45" {{ request('hora') === '14:45' ? 'selected' : '' }}>2:45 p.m.</option>
                                <option value="15:30" {{ request('hora') === '15:30' ? 'selected' : '' }}>3:30 p.m.</option>
                            </select>
                        </div>
                    </div>

                    <!-- SEGUNDA FILA: FECHA INICIO, FECHA FIN Y REGISTROS POR PÁGINA -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full pt-3 border-t border-gray-100">
                        <!-- 5. FECHA INICIO -->
                        <div>
                            <label for="fecha_inicio" class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha Inicio Reservas</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ $fechaInicio }}"
                                class="w-full h-10 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-amber-50/30" />
                        </div>

                        <!-- 6. FECHA FIN -->
                        <div>
                            <label for="fecha_fin" class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha Fin Reservas</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $fechaFin }}"
                                class="w-full h-10 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-amber-50/30" />
                        </div>

                        <!-- 7. COMBO SELECTOR DE REGISTROS POR PÁGINA -->
                        <div>
                            <label for="per_page" class="block text-xs font-semibold text-gray-600 mb-1.5">Registros por Página</label>
                            <select name="per_page" id="per_page" onchange="this.form.submit()"
                                class="w-full h-10 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 font-medium">
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 por página (por defecto)</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 por página</option>
                                <option value="75" {{ $perPage == 75 ? 'selected' : '' }}>75 por página</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 por página</option>
                            </select>
                        </div>
                    </div>

                    <!-- TERCERA FILA: BOTONES DE ACCIÓN Y NOTA DE FILTRO -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full pt-3 border-t border-gray-100">
                        <div class="text-xs text-gray-500 font-medium">
                            @if(!$hasCustomDateFilter)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 font-semibold gap-1 border border-indigo-100">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Mostrando automáticamente las reservaciones del Día Actual ({{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }})
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-amber-100 text-amber-800 font-semibold gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Filtro Rango Reservaciones: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <button type="submit"
                                class="flex-1 sm:flex-none h-10 inline-flex items-center justify-center px-6 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm gap-2 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Filtrar Resultados
                            </button>

                            @if ($hasFilters)
                                <a href="{{ route('reportes.reservas') }}"
                                    class="h-10 inline-flex items-center justify-center px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition whitespace-nowrap"
                                    title="Limpiar filtros (Regresa al día actual por defecto)">
                                    Limpiar Filtros
                                </a>
                            @endif
                        </div>
                    </div>

                </form>
            </div>

            <!-- TABLA DE RESERVACIONES DETALLADAS -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                <!-- PAGINADOR SUPERIOR -->
                @if ($reservas->hasPages())
                    <div class="px-6 py-3 border-b border-gray-100 bg-gray-50/80 flex flex-col sm:flex-row justify-between items-center gap-2">
                        <span class="text-xs text-gray-500 font-medium">
                            Mostrando del <strong>{{ $reservas->firstItem() }}</strong> al
                            <strong>{{ $reservas->lastItem() }}</strong> de
                            <strong>{{ number_format($reservas->total()) }}</strong> reservaciones registradas
                        </span>
                        <div class="text-xs">
                            {{ $reservas->links() }}
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Nº Empleado
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Colaborador
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Correo Electrónico
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Departamento / Puesto
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Horario Reservado
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Fecha Reservación
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Fecha de Registro
                                </th>
                                <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Estatus
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($reservas as $reserva)
                                @php
                                    $emp = $reserva->empleado;
                                    $fechaObj = $reserva->fecha ? \Carbon\Carbon::parse($reserva->fecha) : null;
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition">
                                    <!-- Nº EMPLEADO -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 tracking-wider">
                                        {{ $emp->numero_empleado ?? '-' }}
                                    </td>

                                    <!-- NOMBRE -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $emp->nombre ?? 'Sin nombre' }}</div>
                                    </td>

                                    <!-- CORREO -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $emp->correo ?? '-' }}
                                    </td>

                                    <!-- DEPARTAMENTO Y PUESTO -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div class="font-medium text-gray-900">{{ $emp->departamento ?? 'Sin departamento' }}</div>
                                        <div class="text-xs text-gray-400">{{ $emp->puesto ?? 'Sin puesto' }}</div>
                                    </td>

                                    <!-- HORARIO RESERVADO -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-2xs font-mono">
                                            🕒 {{ $reserva->hora ? $reserva->hora . ' p.m.' : '-' }}
                                        </span>
                                    </td>

                                    <!-- FECHA DE RESERVACIÓN -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900">
                                        {{ $fechaObj ? $fechaObj->format('d/m/Y') : '-' }}
                                    </td>

                                    <!-- FECHA DE REGISTRO (CREACIÓN) -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium text-gray-600 font-mono">
                                        {{ $reserva->created_at ? $reserva->created_at->format('d/m/Y H:i:s') : '-' }}
                                    </td>

                                    <!-- ESTATUS EMPLEADO -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if ($emp && $emp->activo)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-base font-semibold text-gray-700">No se encontraron reservaciones para esta fecha</p>
                                        <p class="text-xs text-gray-400 mt-1">Intente cambiando la fecha o ajustando los criterios de búsqueda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINADOR INFERIOR -->
                @if ($reservas->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $reservas->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
