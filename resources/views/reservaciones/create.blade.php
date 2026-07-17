<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reservación de Comedor') }}
        </h2>
    </x-slot>

    <!-- Estilos de fuentes e identidad visual -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap');
        
        .font-brand-logo-cursive {
            font-family: 'Dancing Script', cursive;
        }
        .font-brand-logo-serif {
            font-family: 'Playfair Display', serif;
        }
    </style>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                
                <!-- TARJETA DEL FORMULARIO DE RESERVACIONES -->
                <div class="lg:col-span-7 bg-white p-8 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-center">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        Haz tu reservación
                    </h2>

                    @if(!$reservasAbiertas)
                        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl flex items-start gap-3">
                            <span class="text-amber-500 mt-0.5">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-sm">Reservaciones no iniciadas</h4>
                                <p class="text-xs mt-0.5">El registro de reservaciones diarias está programado para iniciar a las 8:30 a.m.</p>
                            </div>
                        </div>
                    @endif

                    <form id="reservacion-form" action="{{ route('reservaciones.store') }}" method="POST" class="space-y-6" x-data="{ 
                        selectedHora: '{{ old('hora', '') }}'
                    }" @submit.prevent="confirmarReservacion($event)">
                        @csrf

                        <!-- NÚMERO DE COLABORADOR -->
                        <div class="space-y-2">
                            <label for="numero_empleado" class="block text-sm font-semibold text-gray-700">
                                Número de colaborador
                            </label>
                            <div class="flex rounded-xl border-2 border-indigo-100 bg-gray-50 overflow-hidden focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition duration-200">
                                <span class="inline-flex items-center pl-4 pr-2 text-indigo-400">
                                    <!-- User Icon -->
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </span>
                                <input
                                    type="text"
                                    name="numero_empleado"
                                    id="numero_empleado"
                                    value="{{ old('numero_empleado') }}"
                                    placeholder="Ingrese su número de colaborador"
                                    maxlength="10"
                                    pattern="[0-9]{1,10}"
                                    title="Debe contener hasta 10 dígitos numéricos"
                                    required
                                    class="border-0 bg-transparent flex-1 block w-full text-gray-700 placeholder-gray-400 font-medium py-3 px-2 focus:ring-0 focus:outline-none"
                                />
                            </div>
                        </div>

                        <!-- CORREO ELECTRÓNICO -->
                        <div class="space-y-2">
                            <label for="correo" class="block text-sm font-semibold text-gray-700">
                                Correo electrónico registrado
                            </label>
                            <div class="flex rounded-xl border-2 border-indigo-100 bg-gray-50 overflow-hidden focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition duration-200">
                                <span class="inline-flex items-center pl-4 pr-2 text-indigo-400">
                                    <!-- Mail Icon -->
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <input
                                    type="email"
                                    name="correo"
                                    id="correo"
                                    value="{{ old('correo') }}"
                                    placeholder="ejemplo@correo.com"
                                    required
                                    class="border-0 bg-transparent flex-1 block w-full text-gray-700 placeholder-gray-400 font-medium py-3 px-2 focus:ring-0 focus:outline-none"
                                />
                            </div>
                        </div>

                        <!-- FECHA -->
                        <div class="space-y-2">
                            <span class="block text-sm font-semibold text-gray-700">
                                Fecha de reservación
                            </span>
                            <div class="flex items-center gap-3 p-3.5 rounded-xl border-2 border-indigo-100 bg-indigo-50/30 text-indigo-900 font-semibold transition duration-200">
                                <span class="text-indigo-500">
                                    <!-- Calendar Icon -->
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <span>Hoy: {{ \Carbon\Carbon::today()->translatedFormat('j \d\e F \d\e Y') }}</span>
                            </div>
                        </div>

                        <!-- Campo oculto para enviar la hora seleccionada -->
                        <input type="hidden" name="hora" :value="selectedHora" required />

                        <!-- HORA BUTTONS SECTION -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">
                                Selección rápida de hora
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <!-- 12:30 Button -->
                                <button
                                    type="button"
                                    @click="selectedHora = '12:30'"
                                    @if(!$horariosStatus['12:30']['habilitado']) disabled @endif
                                    :class="selectedHora === '12:30' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : (@json(!$horariosStatus['12:30']['habilitado']) ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100')"
                                    class="py-3 px-1 border rounded-xl font-semibold text-sm text-center transition duration-200 focus:outline-none flex flex-col items-center justify-center gap-0.5"
                                >
                                    <span>12:30 p.m.</span>
                                    <span class="text-[10px] {{ old('hora') === '12:30' ? 'text-indigo-200' : 'text-gray-500' }}" :class="selectedHora === '12:30' ? 'text-indigo-200' : ''">
                                        @if(!$horariosStatus['12:30']['habilitado'])
                                            {{ $horariosStatus['12:30']['mensaje'] }}
                                        @else
                                            {{ $horariosStatus['12:30']['libres'] }} libres
                                        @endif
                                    </span>
                                </button>

                                <!-- 13:45 Button -->
                                <button
                                    type="button"
                                    @click="selectedHora = '13:45'"
                                    @if(!$horariosStatus['13:45']['habilitado']) disabled @endif
                                    :class="selectedHora === '13:45' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : (@json(!$horariosStatus['13:45']['habilitado']) ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100')"
                                    class="py-3 px-1 border rounded-xl font-semibold text-sm text-center transition duration-200 focus:outline-none flex flex-col items-center justify-center gap-0.5"
                                >
                                    <span>13:45 p.m.</span>
                                    <span class="text-[10px] {{ old('hora') === '13:45' ? 'text-indigo-200' : 'text-gray-500' }}" :class="selectedHora === '13:45' ? 'text-indigo-200' : ''">
                                        @if(!$horariosStatus['13:45']['habilitado'])
                                            {{ $horariosStatus['13:45']['mensaje'] }}
                                        @else
                                            {{ $horariosStatus['13:45']['libres'] }} libres
                                        @endif
                                    </span>
                                </button>

                                <!-- 14:45 Button -->
                                <button
                                    type="button"
                                    @click="selectedHora = '14:45'"
                                    @if(!$horariosStatus['14:45']['habilitado']) disabled @endif
                                    :class="selectedHora === '14:45' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : (@json(!$horariosStatus['14:45']['habilitado']) ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100')"
                                    class="py-3 px-1 border rounded-xl font-semibold text-sm text-center transition duration-200 focus:outline-none flex flex-col items-center justify-center gap-0.5"
                                >
                                    <span>14:45 p.m.</span>
                                    <span class="text-[10px] {{ old('hora') === '14:45' ? 'text-indigo-200' : 'text-gray-500' }}" :class="selectedHora === '14:45' ? 'text-indigo-200' : ''">
                                        @if(!$horariosStatus['14:45']['habilitado'])
                                            {{ $horariosStatus['14:45']['mensaje'] }}
                                        @else
                                            {{ $horariosStatus['14:45']['libres'] }} libres
                                        @endif
                                    </span>
                                </button>

                                <!-- 15:45 Button -->
                                <button
                                    type="button"
                                    @click="selectedHora = '15:45'"
                                    @if(!$horariosStatus['15:45']['habilitado']) disabled @endif
                                    :class="selectedHora === '15:45' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : (@json(!$horariosStatus['15:45']['habilitado']) ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100')"
                                    class="py-3 px-1 border rounded-xl font-semibold text-sm text-center transition duration-200 focus:outline-none flex flex-col items-center justify-center gap-0.5"
                                >
                                    <span>15:45 p.m.</span>
                                    <span class="text-[10px] {{ old('hora') === '15:45' ? 'text-indigo-200' : 'text-gray-500' }}" :class="selectedHora === '15:45' ? 'text-indigo-200' : ''">
                                        @if(!$horariosStatus['15:45']['habilitado'])
                                            {{ $horariosStatus['15:45']['mensaje'] }}
                                        @else
                                            {{ $horariosStatus['15:45']['libres'] }} libres
                                        @endif
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- BOTÓN RESERVAR -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                class="w-full flex items-center justify-center gap-2 py-3.5 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-900 text-white rounded-xl font-semibold text-base transition duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm"
                            >
                                <!-- Calendar Check Icon -->
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Reservar
                            </button>
                        </div>

                    </form>
                </div>

                <!-- PANEL INFORMATIVO Y LOGOTIPO -->
                <div class="lg:col-span-5 bg-white p-8 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <!-- Logotipo Comedor GILOU -->
                        <div class="flex flex-col mb-8 relative select-none">
                            <div class="relative inline-block">
                                <!-- "Comedor" en manuscrito rojo -->
                                <span class="font-brand-logo-cursive text-3xl text-indigo-600 absolute -top-5 left-16 transform rotate-[-6deg] z-10">
                                    Comedor
                                </span>
                                <!-- "GILOU" Serif elegante -->
                                <span class="font-brand-logo-serif text-6xl font-bold tracking-wider text-gray-900 block">
                                    GILOU
                                </span>
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-gray-800 mb-4">
                            Reserva tu lugar en el comedor
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed mb-8">
                            Para asegurar una mejor atención y coordinar el servicio de alimentos diariamente, por favor registre su reservación ingresando su número de colaborador y seleccionando el horario de su preferencia. Las reservaciones se realizan exclusivamente para el día de hoy.
                        </p>

                        <!-- Puntos de guía -->
                        <div class="space-y-4 text-xs text-gray-500">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span>4 Horarios Disponibles: 12:30 p.m., 13:45 p.m., 14:45 p.m. y 15:45 p.m.</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span>Límite de 180 lugares por cada horario diariamente.</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span>Límite de 1 reservación por día por colaborador.</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <span>El colaborador debe estar registrado y activo en el sistema.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Nota al pie -->
                    <div class="mt-8 pt-4 border-t border-gray-100 text-xs text-gray-400">
                        © {{ date('Y') }} Comedor GILOU. Todos los derechos reservados.
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if (session('success_reservation'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Reservación Exitosa!',
                    html: `
                        <div class="text-left mt-2 p-3 bg-gray-50 rounded-lg border border-gray-100 text-sm space-y-2">
                            <div><span class="font-bold text-gray-500">Colaborador:</span> <span class="text-gray-900 font-semibold">{{ session('success_reservation')['empleado'] }}</span></div>
                            <div><span class="font-bold text-gray-500">Fecha:</span> <span class="text-gray-900 font-semibold">{{ \Carbon\Carbon::parse(session('success_reservation')['fecha'])->format('d/m/Y') }}</span></div>
                            <div><span class="font-bold text-gray-500">Horario:</span> <span class="text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded text-xs">{{ session('success_reservation')['hora'] }} p.m.</span></div>
                        </div>
                    `,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#4f46e5',
                    customClass: {
                        popup: 'rounded-2xl border border-gray-100'
                    }
                });
            });
        </script>
    @endif

    <script>
        function confirmarReservacion(event) {
            const form = event.target;
            const hora = form.querySelector('input[name="hora"]').value;
            const numEmp = document.getElementById('numero_empleado').value.trim();
            const correo = document.getElementById('correo').value.trim();

            // Validación de horario de apertura (8:30 a.m.)
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();
            const currentTimeMinutes = currentHour * 60 + currentMinute;

            // 8:30 AM is 8 * 60 + 30 = 510 minutes
            if (currentTimeMinutes < 110) {
                Swal.fire({
                    icon: 'error',
                    title: 'Reservaciones no iniciadas',
                    text: 'El horario para empezar la reserva solo puede ser después de las 8:30 a.m.',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            if (!hora) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Horario requerido',
                    text: 'Por favor, elija uno de los cuatro horarios disponibles.',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            // Validación de límites de 15 minutos de anticipación por horario
            const limits = {
                '12:30': 12 * 60 + 15, // 12:15
                '13:45': 13 * 60 + 30, // 13:30
                '14:45': 14 * 60 + 30, // 14:30
                '15:45': 15 * 60 + 30  // 15:30
            };

            if (limits[hora] && currentTimeMinutes >= limits[hora]) {
                Swal.fire({
                    icon: 'error',
                    title: 'Horario expirado',
                    text: 'El tiempo límite para reservar el horario de las ' + hora + ' p.m. ha expirado.',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            if (!numEmp) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Número requerido',
                    text: 'Por favor, ingrese su número de colaborador.',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            if (!correo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Correo requerido',
                    text: 'Por favor, ingrese su correo electrónico registrado.',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            Swal.fire({
                title: 'Cargando...',
                text: 'Buscando datos del colaborador',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const url = "{{ route('reservaciones.empleado_info', ['numero_empleado' => ':num']) }}".replace(':num', numEmp) + '?correo=' + encodeURIComponent(correo);
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error de conexión con el servidor.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        if (data.already_reserved) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Ya tienes una reservación hoy',
                                text: data.message,
                                confirmButtonColor: '#4f46e5',
                                customClass: {
                                    popup: 'rounded-2xl border border-gray-100'
                                }
                            });
                            return;
                        }
                        
                        // Error de discrepancia o de no registrado
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de validación',
                            text: data.message || 'El número de colaborador o correo no pertenecen al registro.',
                            confirmButtonColor: '#4f46e5',
                            customClass: {
                                popup: 'rounded-2xl border border-gray-100'
                            }
                        });
                        return;
                    }
                    Swal.close();
                    Swal.fire({
                        title: '¿Confirmar Reservación?',
                        html: `
                            <div class="text-left mt-2 p-3 bg-gray-50 rounded-lg border border-gray-100 text-sm space-y-2">
                                <div><span class="font-bold text-gray-500">Colaborador:</span> <span class="text-gray-900 font-semibold">${data.nombre}</span></div>
                                <div><span class="font-bold text-gray-500">Fecha:</span> <span class="text-gray-900 font-semibold">${new Date().toLocaleDateString('es-MX', {day: '2-digit', month: '2-digit', year: 'numeric'})}</span></div>
                                <div><span class="font-bold text-gray-500">Horario:</span> <span class="text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded text-xs">${hora} p.m.</span></div>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Reservar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#4f46e5',
                        cancelButtonColor: '#ef4444',
                        customClass: {
                            popup: 'rounded-2xl border border-gray-100'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message,
                        confirmButtonColor: '#4f46e5'
                    });
                });
        }
    </script>
</x-app-layout>
