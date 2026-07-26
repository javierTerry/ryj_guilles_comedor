<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    {{ __('Reporte de Encuestas de Satisfacción') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Consulta detallada de las evaluaciones del servicio del comedor, filtradas por la semana actual por defecto.
                </p>
            </div>
            <a href="{{ route('reportes.encuestas_export', request()->query()) }}"
                class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-sm transition duration-150 gap-2"
                title="Descargar reporte de encuestas en formato CSV para Excel">
                <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar Encuestas CSV
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="w-full max-w-[95%] lg:max-w-[90%] mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- PESTAÑAS DE NAVEGACIÓN DE REPORTES -->
            <div class="border-b border-gray-200 bg-white px-4 rounded-xl shadow-sm">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('reportes.index') }}"
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 00-2 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Reporte General (Resumen)
                    </a>
                    <a href="{{ route('reportes.visitas') }}"
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Reporte de Visitas (Detalle e Ingresos)
                    </a>
                    <a href="{{ route('reportes.encuestas') }}"
                       class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2"
                       aria-current="page">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        Reporte de Encuestas (Satisfacción)
                    </a>
                </nav>
            </div>

            <!-- TARJETAS DE RESUMEN KPI -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- 1. Total Encuestas -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Encuestas</p>
                        <h3 class="text-3xl font-extrabold text-indigo-600 mt-1">
                            {{ number_format($totalEncuestas) }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Respuestas registradas</p>
                    </div>
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>

                <!-- 2. Promedio Calificación -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Promedio Calificación</p>
                        <h3 class="text-3xl font-extrabold text-amber-500 mt-1 flex items-center gap-1">
                            {{ number_format($promedioCalificacion, 2) }}
                            <span class="text-base font-bold text-amber-400">★</span>
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Escala de 1 a 5 estrellas</p>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-500 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                </div>

                <!-- 3. Promedio Conversión (%) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Promedio Conversión</p>
                        <h3 class="text-3xl font-extrabold text-emerald-600 mt-1">
                            {{ number_format($promedioConversion, 1) }}%
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Satisfacción Global %</p>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>

                <!-- 4. Rango de Fechas -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Rango de Consulta</p>
                        <h3 class="text-sm font-bold text-slate-800 mt-1">
                            Del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            @if(!$hasCustomDateFilter)
                                <span class="text-indigo-600 font-semibold">🗓️ Semana Actual (Por Defecto)</span>
                            @else
                                Personalizado por filtro
                            @endif
                        </p>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
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

                <form method="GET" action="{{ route('reportes.encuestas') }}" class="space-y-4 w-full">

                    <!-- PRIMERA FILA: BÚSQUEDA, DEPARTAMENTO Y ESTATUS -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full pt-3 border-t border-gray-100">
                        <!-- 1. BÚSQUEDA POR NOMBRE / NÚMERO -->
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <label for="search" class="block text-xs font-semibold text-gray-600 mb-1.5">Nombre o Nº Empleado</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Ej: 1024 o Juan"
                                class="w-full h-10 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <!-- 2. FILTRO DEPARTAMENTO -->
                        <div class="flex items-center gap-3 w-full sm:w-auto">
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
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <label for="estatus" class="block text-xs font-semibold text-gray-600 mb-1.5">Estatus Empleado</label>
                            <select name="estatus" id="estatus"
                                class="w-full h-10 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos los estatus</option>
                                <option value="1" {{ request('estatus') === '1' ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ request('estatus') === '0' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <!-- SEGUNDA FILA: FECHA INICIO, FECHA FIN Y REGISTROS POR PÁGINA -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full pt-3 border-t border-gray-100">
                        <!-- 4. FECHA INICIO -->
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <label for="fecha_inicio" class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha Inicio Encuestas</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ $fechaInicio }}"
                                class="w-full h-10 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-amber-50/30" />
                        </div>

                        <!-- 5. FECHA FIN -->
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <label for="fecha_fin" class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha Fin Encuestas</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $fechaFin }}"
                                class="w-full h-10 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-amber-50/30" />
                        </div>

                        <!-- 6. COMBO SELECTOR DE REGISTROS POR PÁGINA -->
                        <div class="flex items-center gap-3 w-full sm:w-auto">
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
                                    Mostrando automáticamente encuestas de la Semana Actual ({{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }})
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-amber-100 text-amber-800 font-semibold gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Filtro Rango Encuestas: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
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
                                <a href="{{ route('reportes.encuestas') }}"
                                    class="h-10 inline-flex items-center justify-center px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition whitespace-nowrap"
                                    title="Limpiar filtros (Regresa a la semana actual por defecto)">
                                    Limpiar Filtros
                                </a>
                            @endif
                        </div>
                    </div>

                </form>
            </div>

            <!-- TABLA DE ENCUESTAS DETALLADAS -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                <!-- PAGINADOR SUPERIOR -->
                @if ($encuestas->hasPages())
                    <div class="px-6 py-3 border-b border-gray-100 bg-gray-50/80 flex flex-col sm:flex-row justify-between items-center gap-2">
                        <span class="text-xs text-gray-500 font-medium">
                            Mostrando del <strong>{{ $encuestas->firstItem() }}</strong> al
                            <strong>{{ $encuestas->lastItem() }}</strong> de
                            <strong>{{ number_format($encuestas->total()) }}</strong> encuestas registradas
                        </span>
                        <div class="text-xs">
                            {{ $encuestas->links() }}
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Colaborador / Depto
                                </th>
                                <th scope="col" class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Fecha / Hora
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider" title="Sabor, frescura y variedad (30%)">
                                    Calidad
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider" title="Instalaciones y manipulación (25%)">
                                    Limpieza
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider" title="≥60°C calientes / ≤7°C fríos (20%)">
                                    Temp.
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider" title="Tiempo de espera y trato (15%)">
                                    Atención
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider" title="Línea fría y caliente (10%)">
                                    Present.
                                </th>
                                <th scope="col" class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Promedio
                                </th>
                                <th scope="col" class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Conversión
                                </th>
                                <th scope="col" class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Comentarios
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($encuestas as $encuesta)
                                @php
                                    $emp = $encuesta->empleado;
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition">
                                    <!-- COLABORADOR / DEPTO -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 text-xs font-bold bg-indigo-50 text-indigo-700 rounded-md font-mono">
                                                #{{ $emp->numero_empleado ?? '-' }}
                                            </span>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $emp->nombre ?? 'Sin nombre' }}</div>
                                                <div class="text-xs text-gray-500">{{ $emp->departamento ?? 'General' }} • {{ $emp->puesto ?? 'Colaborador' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- FECHA / HORA -->
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-xs font-bold text-gray-800">
                                                {{ $encuesta->fecha ? \Carbon\Carbon::parse($encuesta->fecha)->format('d/m/Y') : '-' }}
                                            </span>
                                            <span class="text-[11px] font-mono text-gray-500">
                                                🕒 {{ $encuesta->hora ?? '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- CALIDAD -->
                                    <td class="px-3 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex flex-col items-center">
                                            <span class="text-xs font-bold text-amber-600">{{ $encuesta->calidad_alimentos }}★</span>
                                            <span class="text-[10px] text-gray-400">{{ number_format($encuesta->calidad_alimentos_conversion, 0) }}%</span>
                                        </span>
                                    </td>

                                    <!-- LIMPIEZA -->
                                    <td class="px-3 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex flex-col items-center">
                                            <span class="text-xs font-bold text-amber-600">{{ $encuesta->limpieza_higiene }}★</span>
                                            <span class="text-[10px] text-gray-400">{{ number_format($encuesta->limpieza_higiene_conversion, 0) }}%</span>
                                        </span>
                                    </td>

                                    <!-- TEMPERATURA -->
                                    <td class="px-3 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex flex-col items-center">
                                            <span class="text-xs font-bold text-amber-600">{{ $encuesta->temperatura_adecuada }}★</span>
                                            <span class="text-[10px] text-gray-400">{{ number_format($encuesta->temperatura_adecuada_conversion, 0) }}%</span>
                                        </span>
                                    </td>

                                    <!-- ATENCIÓN -->
                                    <td class="px-3 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex flex-col items-center">
                                            <span class="text-xs font-bold text-amber-600">{{ $encuesta->atencion_eficiencia }}★</span>
                                            <span class="text-[10px] text-gray-400">{{ number_format($encuesta->atencion_eficiencia_conversion, 0) }}%</span>
                                        </span>
                                    </td>

                                    <!-- PRESENTACIÓN -->
                                    <td class="px-3 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex flex-col items-center">
                                            <span class="text-xs font-bold text-amber-600">{{ $encuesta->presentacion }}★</span>
                                            <span class="text-[10px] text-gray-400">{{ number_format($encuesta->presentacion_conversion, 0) }}%</span>
                                        </span>
                                    </td>

                                    <!-- PROMEDIO -->
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 font-extrabold text-xs">
                                            {{ number_format($encuesta->calificacion, 2) }} ★
                                        </span>
                                    </td>

                                    <!-- CONVERSIÓN COMPUESTA % -->
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 font-extrabold text-xs">
                                            {{ number_format($encuesta->conversion, 1) }}%
                                        </span>
                                    </td>

                                    <!-- COMENTARIOS -->
                                    <td class="px-4 py-4 whitespace-nowrap text-center text-xs">
                                        @if ($encuesta->comentarios)
                                            <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-700 cursor-pointer max-w-[150px] truncate" title="{{ $encuesta->comentarios }}">
                                                💬 {{ Str::limit($encuesta->comentarios, 25) }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        <p class="text-base font-semibold text-gray-700">No se encontraron encuestas registradas en este período</p>
                                        <p class="text-xs text-gray-400 mt-1">Intente cambiando el rango de fechas o ajustando los filtros de búsqueda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINADOR INFERIOR -->
                @if ($encuestas->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $encuestas->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
