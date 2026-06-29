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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Grid Layout: Scanner & Success Card on Top -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Scanner Control Box -->
                <div id="scanner-card" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between cursor-pointer">
                    <div>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">
                            Lector de Código / Entrada
                        </h3>
                        <p class="text-xs text-gray-500 mb-6">
                            Escanee el código de barras del gafete del empleado o escriba sus 10 dígitos y presione <kbd class="px-1.5 py-0.5 bg-gray-100 border rounded font-mono text-gray-600">Enter</kbd>.
                        </p>
                    </div>

                    <form action="{{ route('comedor.registrar') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="numero_empleado" class="sr-only">Número de Empleado</label>
                            <input
                                x-ref="employeeInput"
                                type="text"
                                name="numero_empleado"
                                id="numero_empleado"
                                placeholder="0000000000"
                                maxlength="10"
                                autocomplete="off"
                                required
                                class="block w-full text-center tracking-widest text-2xl font-bold py-3.5 border-2 border-indigo-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl bg-slate-50 transition"
                            />
                        </div>

                        <button
                            type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none transition shadow-sm"
                        >
                            Registrar Entrada
                        </button>
                    </form>
                </div>

                <!-- Status Feedback Panels -->
                <div class="md:col-span-2 flex flex-col justify-stretch">
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
                        <div class="bg-emerald-500 text-white rounded-xl shadow-sm overflow-hidden flex flex-col h-full animate-pulse-once">
                            <div class="p-6 flex-1 flex flex-col justify-between">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-sm mb-2 border border-emerald-400">
                                            ✔ ACCESO AUTORIZADO
                                        </span>
                                        <h2 class="text-2xl font-extrabold tracking-tight mt-1">{{ $emp->nombre }}</h2>
                                        <p class="text-emerald-100 text-sm mt-0.5">Número de Empleado: <span class="font-mono font-bold">{{ $emp->numero_empleado }}</span></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-3xl font-black tracking-wider">{{ $time }}</p>
                                        <p class="text-xs text-emerald-100 font-medium">Hora de Registro</p>
                                    </div>
                                </div>

                                <div class="mt-6 pt-4 border-t border-emerald-400/50 grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-emerald-100 uppercase font-semibold">Departamento</p>
                                        <p class="text-sm font-bold">{{ $emp->departamento ?? 'Sin departamento' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-emerald-100 uppercase font-semibold">Comidas Acumuladas</p>
                                        <p class="text-sm font-bold">{{ $total }} {{ Str::plural('visita', $total) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-emerald-600 px-6 py-2.5 text-center text-xs font-semibold text-emerald-50 tracking-wider">
                                Buen provecho • Menú del día preestablecido
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
                            <div class="p-6 flex-1 flex flex-col justify-between">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-600 text-white shadow-sm mb-2 border border-rose-400">
                                        ❌ ACCESO DENEGADO
                                    </span>
                                    <h2 class="text-2xl font-extrabold tracking-tight mt-1">{{ $emp->nombre }}</h2>
                                    <p class="text-rose-100 text-sm mt-0.5">Número de Empleado: <span class="font-mono font-bold">{{ $emp->numero_empleado }}</span></p>
                                </div>

                                <div class="mt-4 p-3 bg-rose-600/50 rounded-lg border border-rose-400/30">
                                    <p class="text-sm font-medium text-rose-50">
                                        {{ session('error_duplicated') }}
                                    </p>
                                </div>
                            </div>
                            <div class="bg-rose-600 px-6 py-2.5 text-center text-xs font-semibold text-rose-50 tracking-wider uppercase">
                                Ya consumió alimentos hoy • Solo se permite 1 comida diaria
                            </div>
                        </div>

                    <!-- OTHER GENERAL ERRORS (INACTIVE, NOT FOUND, VALIDATION ERRORS) -->
                    @elseif (session('error') || $errors->any())
                        <div class="bg-amber-500 text-white rounded-xl shadow-sm overflow-hidden flex flex-col h-full">
                            <div class="p-6 flex-1 flex flex-col justify-center items-center text-center">
                                <svg class="w-14 h-14 text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <h2 class="text-xl font-bold tracking-tight">Acceso Denegado / Error</h2>
                                <p class="text-amber-50 text-sm mt-2 max-w-md">
                                    @if (session('error'))
                                        {{ session('error') }}
                                    @else
                                        Por favor, revise que el número ingresado tenga exactamente 10 dígitos y sea numérico.
                                    @endif
                                </p>
                            </div>
                            <div class="bg-amber-600 px-6 py-2.5 text-center text-xs font-semibold text-amber-50 tracking-wider">
                                Favor de verificar credenciales o contactar a Recursos Humanos
                            </div>
                        </div>

                    <!-- IDLE STATE CARD -->
                    @else
                        <div class="bg-slate-100 border border-dashed border-slate-300 rounded-xl flex flex-col justify-center items-center text-center p-6 h-full min-h-[220px]">
                            <svg class="w-16 h-16 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-2 5h.01M9 16h.01M9 12h.01M12 12h.01M12 16h.01M15 12h.01M15 16h.01"></path>
                            </svg>
                            <h3 class="text-md font-bold text-slate-700">Esperando Escaneo...</h3>
                            <p class="text-sm text-slate-500 mt-1 max-w-sm">
                                Los datos del empleado y el estado de su autorización se mostrarán aquí una vez que escanee su código.
                            </p>
                        </div>
                    @endif
                </div>

            </div>

            <!-- TABLE OF TODAY'S ACCESS LOGS -->
            @auth
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider">
                        Historial de Comidas Registradas Hoy ({{ count($registros) }})
                    </h3>
                    <span class="px-2.5 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded-full text-xs font-semibold">
                        {{ Carbon\Carbon::today()->format('d/m/Y') }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Hora
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Nº Empleado
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Nombre
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Departamento
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Puesto
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($registros as $registro)
                                <tr class="hover:bg-gray-50/40 transition">
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
                                    <td colspan="5" class="px-6 py-12 whitespace-nowrap text-center text-sm text-gray-400">
                                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
