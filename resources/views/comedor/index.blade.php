<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registro de Acceso al Comedor') }}
        </h2>
    </x-slot>

    <div class="py-8" x-data="{
        initFocus() {
            this.$refs.employeeInput.focus();
            // Automatically focus input if user clicks anywhere on the card background to avoid losing focus
            document.addEventListener('click', (e) => {
                if (e.target.closest('#scanner-card')) {
                    this.$refs.employeeInput.focus();
                }
            });
        }
    }" x-init="initFocus()">
        <div class="w-full max-w-[95%] lg:max-w-[90%] mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flex Layout: Responsive 3-Card Row with 30%, 50%, and 20% Proportions (3:5:2 Ratio without overflow) -->
            <div class="flex flex-col lg:flex-row gap-6 items-stretch w-full">

                <!-- CARD 1: Scanner Control Box (Proportion: 30% -> flex-[3]) -->
                <div id="scanner-card"
                    class="w-full lg:flex-[3] min-w-0 bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between cursor-pointer">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                                Lector de Código / Entrada
                            </h3>
                            @if(!config('app.require_reservation'))
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 shrink-0"
                                    title="Modo POC activo: Permite el ingreso sin reservación previa">
                                    ⚡ Modo POC
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mb-4">
                            Escanee el código o digite el número de empleado y presione <kbd
                                class="px-1 py-0.5 bg-gray-100 border rounded font-mono text-[10px] text-gray-600">Enter</kbd>.
                        </p>
                    </div>

                    <form action="{{ route('comedor.registrar') }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label for="numero_empleado" class="sr-only">Número de Empleado</label>
                            <input x-ref="employeeInput" type="text" name="numero_empleado" id="numero_empleado"
                                placeholder="Nº Empleado" maxlength="10" autocomplete="off" required
                                class="block w-full text-center tracking-widest text-xl font-bold py-2.5 border-2 border-indigo-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl bg-slate-50 transition" />
                        </div>

                        <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none transition shadow-sm">
                            Registrar Entrada
                        </button>
                    </form>
                </div>

                <!-- CARD 2: Status Feedback Panels (Proportion: 50% -> flex-[5]) -->
                <div id="status-card" class="w-full lg:flex-[5] min-w-0 flex flex-col justify-stretch">
                    <!-- SUCCESS CARD -->
                    @if (session('success'))
                        @php
                            $emp = session('last_registered');
                            if (is_array($emp)) {
                                $emp = (object) $emp;
                            }
                            $time = session('last_registered_time');
                            $total = session('last_registered_total');
                        @endphp
                        <div
                            class="bg-emerald-500 text-white rounded-xl shadow-sm overflow-hidden flex flex-col h-full animate-pulse-once">
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start gap-2 mb-1">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-sm border border-emerald-400 shrink-0">
                                            ✔ ACCESO AUTORIZADO
                                        </span>
                                        <span class="text-xl font-black font-mono shrink-0">{{ $time }}</span>
                                    </div>
                                    <h2 class="text-xl font-extrabold tracking-tight mt-1 truncate"
                                        title="{{ $emp->nombre }}">{{ $emp->nombre }}</h2>
                                    <p class="text-emerald-100 text-xs mt-0.5">Nº Empleado: <span
                                            class="font-mono font-bold text-sm">{{ $emp->numero_empleado }}</span></p>
                                </div>

                                <div
                                    class="mt-3 pt-3 border-t border-emerald-400/50 flex items-center justify-between gap-4 text-xs">
                                    <div class="min-w-0">
                                        <p class="text-[10px] text-emerald-100 uppercase font-semibold">Departamento</p>
                                        <p class="font-bold text-sm truncate">
                                            {{ $emp->departamento ?? 'Sin departamento' }}
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-[10px] text-emerald-100 uppercase font-semibold">Visitas Empleado</p>
                                        <p class="font-bold font-mono text-sm">{{ $total }}
                                            {{ Str::plural('comida', $total) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="bg-emerald-600 px-4 py-2 text-center text-xs font-semibold text-emerald-50 tracking-wider">
                                Buen provecho • Menú preestablecido
                            </div>
                        </div>

                        <!-- DUPLICATED REGISTRATION WARNING -->
                    @elseif (session('error_duplicated'))
                        @php
                            $emp = session('duplicated_employee');
                            if (is_array($emp)) {
                                $emp = (object) $emp;
                            }
                        @endphp
                        <div class="bg-rose-500 text-white rounded-xl shadow-sm overflow-hidden flex flex-col h-full">
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-600 text-white shadow-sm mb-1 border border-rose-400 shrink-0">
                                        ❌ ACCESO DENEGADO
                                    </span>
                                    <h2 class="text-xl font-extrabold tracking-tight mt-1 truncate"
                                        title="{{ $emp->nombre }}">{{ $emp->nombre }}</h2>
                                    <p class="text-rose-100 text-xs mt-0.5">Nº Empleado: <span
                                            class="font-mono font-bold text-sm">{{ $emp->numero_empleado }}</span></p>
                                </div>

                                <div class="mt-2 p-3 bg-rose-600/50 rounded-lg border border-rose-400/30">
                                    <p class="text-xs font-medium text-rose-50 line-clamp-2">
                                        {{ session('error_duplicated') }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="bg-rose-600 px-4 py-2 text-center text-xs font-semibold text-rose-50 tracking-wider uppercase">
                                1 comida diaria permitida
                            </div>
                        </div>

                        <!-- OTHER GENERAL ERRORS (INACTIVE, NOT FOUND, VALIDATION ERRORS) -->
                    @elseif (session('error') || $errors->any())
                        <div class="bg-amber-500 text-white rounded-xl shadow-sm overflow-hidden flex flex-col h-full">
                            <div class="p-5 flex-1 flex flex-col justify-center items-center text-center">
                                <svg class="w-10 h-10 text-white mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                <h2 class="text-lg font-bold tracking-tight">Acceso Denegado</h2>
                                <p class="text-amber-50 text-xs mt-1">
                                    @if (session('error'))
                                        {{ session('error') }}
                                    @else
                                        Número inválido o no encontrado.
                                    @endif
                                </p>
                            </div>
                            <div
                                class="bg-amber-600 px-4 py-2 text-center text-xs font-semibold text-amber-50 tracking-wider">
                                Verificar con Recursos Humanos
                            </div>
                        </div>

                        <!-- IDLE STATE CARD -->
                    @else
                        <div
                            class="bg-slate-100 border border-dashed border-slate-300 rounded-xl flex flex-col justify-center items-center text-center p-5 h-full min-h-[220px]">
                            <svg class="w-12 h-12 text-slate-400 mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-2 5h.01M9 16h.01M9 12h.01M12 12h.01M15 12h.01M15 16h.01">
                                </path>
                            </svg>
                            <h3 class="text-base font-bold text-slate-700">Esperando Escaneo...</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-xs">
                                Los datos del empleado y su estado de autorización se mostrarán aquí.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- CARD 3: Compact Access Counter Card (Proportion: 20% -> flex-[2]) -->
                <div id="counter-card"
                    class="w-full lg:flex-[2] min-w-0 bg-white p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-1 gap-1">
                            <h3
                                class="text-[11px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider truncate">
                                Entradas
                            </h3>
                            <span
                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0"
                                title="Entradas acumuladas hoy en tiempo real">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1 animate-pulse"></span> 
                                {{ Carbon\Carbon::today()->format('d/m/Y') }} 
                            </span>
                        </div>
                        <p class="text-[10px] text-gray-400 truncate">
                            Total al momento
                        </p>
                    </div>

                    <!-- Clean, Compact Number Display -->
                    <div class="my-auto text-center flex flex-col justify-center items-center py-1">
                        <div
                            class="inline-flex items-center justify-center px-2 py-2 bg-indigo-50 rounded-2xl border-2 border-indigo-100 shadow-inner w-full">
                            <span
                                class="text-5xl sm:text-6xl md:text-7xl font-black tracking-tight text-indigo-900 font-mono whitespace-nowrap">
                                {{ count($registros) }}
                            </span>
                        </div>

                    </div>
                </div>

            </div>

            <!-- TABLE OF TODAY'S ACCESS LOGS -->
            @auth
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <div class="flex items-center gap-3">
                            <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider">
                                Historial de Comidas Registradas Hoy
                            </h3>
                            <span
                                class="inline-flex items-center justify-center px-3 py-1 bg-indigo-600 text-white rounded-full text-xs font-extrabold shadow-sm min-w-[2.5rem]"
                                title="Total acumulado de entradas hoy">
                                {{ count($registros) }}
                            </span>
                        </div>
                        <span
                            class="px-2.5 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded-full text-xs font-semibold">
                            {{ Carbon\Carbon::today()->format('d/m/Y') }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Nº Entrada
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Hora
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Nº Empleado
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Departamento
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Puesto
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($registros as $registro)
                                    @php
                                        $numEntrada = $registros->count() - $loop->index;
                                    @endphp
                                    <tr class="hover:bg-gray-50/40 transition">
                                        <!-- BURBUJA CONTADOR REGISTRO DE ENTRADA -->
                                        <td class="px-6 py-3.5 whitespace-nowrap text-center">
                                            <span
                                                class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-full text-base sm:text-lg md:text-xl font-black bg-indigo-100 text-indigo-800 border border-indigo-200 shadow-xs min-w-[3.75rem] sm:min-w-[4.5rem] tracking-tight font-mono whitespace-nowrap"
                                                title="Número de entrada #{{ $numEntrada }}">
                                                #{{ $numEntrada }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ Carbon\Carbon::parse($registro->fecha_hora)->format('H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 tracking-wider">
                                            {{ $registro->empleado->numero_empleado }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-800">
                                                {{ $registro->empleado->nombre }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $registro->empleado->departamento ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $registro->empleado->puesto ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 whitespace-nowrap text-center text-sm text-gray-400">
                                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            No se han registrado comidas el día de hoy.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endauth

        </div>
    </div>
</x-app-layout>