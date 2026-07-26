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
        <div class="w-full max-w-[95%] lg:max-w-[90%] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8 items-stretch">
                
                <!-- TARJETA DEL FORMULARIO DE RESERVACIONES -->
                <div class="md:col-span-5 w-full bg-white p-6 sm:p-8 lg:p-10 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-full">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">
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
                                Selección rápida de horario
                            </label>
                            <div style="display: flex; flex-direction: row; gap: 8px; width: 100%; justify-content: space-between; align-items: stretch;">
                                <!-- 12:30 Button -->
                                <button
                                    type="button"
                                    @click="selectedHora = '12:30'"
                                    @if(!$horariosStatus['12:30']['habilitado']) disabled @endif
                                    :class="selectedHora === '12:30' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md ring-2 ring-indigo-300' : (@json(!$horariosStatus['12:30']['habilitado']) ? 'bg-gray-100/90 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-indigo-50 hover:border-indigo-200')"
                                    style="flex: 1 1 0%; min-width: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 12px 4px; border-radius: 16px; border-width: 1px; border-style: solid; text-align: center; transition: all 0.2s; min-height: 66px;"
                                >
                                    <span class="font-bold text-xs sm:text-sm truncate w-full block" :class="selectedHora === '12:30' ? 'text-white' : ''">12:30 p.m.</span>
                                    <span class="text-[11px] sm:text-xs truncate w-full block mt-0.5" :class="selectedHora === '12:30' ? 'text-indigo-100' : ''">
                                        @if(!$horariosStatus['12:30']['habilitado'])
                                            Cerrado
                                        @else
                                            {{ $horariosStatus['12:30']['libres'] }} libres
                                        @endif
                                    </span>
                                </button>

                                <!-- 13:15 Button -->
                                <button
                                    type="button"
                                    @click="selectedHora = '13:15'"
                                    @if(!$horariosStatus['13:15']['habilitado']) disabled @endif
                                    :class="selectedHora === '13:15' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md ring-2 ring-indigo-300' : (@json(!$horariosStatus['13:15']['habilitado']) ? 'bg-gray-100/90 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-indigo-50 hover:border-indigo-200')"
                                    style="flex: 1 1 0%; min-width: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 12px 4px; border-radius: 16px; border-width: 1px; border-style: solid; text-align: center; transition: all 0.2s; min-height: 66px;"
                                >
                                    <span class="font-bold text-xs sm:text-sm truncate w-full block" :class="selectedHora === '13:15' ? 'text-white' : ''">1:15 p.m.</span>
                                    <span class="text-[11px] sm:text-xs truncate w-full block mt-0.5" :class="selectedHora === '13:15' ? 'text-indigo-100' : ''">
                                        @if(!$horariosStatus['13:15']['habilitado'])
                                            Cerrado
                                        @else
                                            {{ $horariosStatus['13:15']['libres'] }} libres
                                        @endif
                                    </span>
                                </button>

                                <!-- 14:00 Button -->
                                <button
                                    type="button"
                                    @click="selectedHora = '14:00'"
                                    @if(!$horariosStatus['14:00']['habilitado']) disabled @endif
                                    :class="selectedHora === '14:00' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md ring-2 ring-indigo-300' : (@json(!$horariosStatus['14:00']['habilitado']) ? 'bg-gray-100/90 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-indigo-50 hover:border-indigo-200')"
                                    style="flex: 1 1 0%; min-width: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 12px 4px; border-radius: 16px; border-width: 1px; border-style: solid; text-align: center; transition: all 0.2s; min-height: 66px;"
                                >
                                    <span class="font-bold text-xs sm:text-sm truncate w-full block" :class="selectedHora === '14:00' ? 'text-white' : ''">2:00 p.m.</span>
                                    <span class="text-[11px] sm:text-xs truncate w-full block mt-0.5" :class="selectedHora === '14:00' ? 'text-indigo-100' : ''">
                                        @if(!$horariosStatus['14:00']['habilitado'])
                                            Cerrado
                                        @else
                                            {{ $horariosStatus['14:00']['libres'] }} libres
                                        @endif
                                    </span>
                                </button>

                                <!-- 14:45 Button -->
                                <button
                                    type="button"
                                    @click="selectedHora = '14:45'"
                                    @if(!$horariosStatus['14:45']['habilitado']) disabled @endif
                                    :class="selectedHora === '14:45' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md ring-2 ring-indigo-300' : (@json(!$horariosStatus['14:45']['habilitado']) ? 'bg-gray-100/90 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-indigo-50 hover:border-indigo-200')"
                                    style="flex: 1 1 0%; min-width: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 12px 4px; border-radius: 16px; border-width: 1px; border-style: solid; text-align: center; transition: all 0.2s; min-height: 66px;"
                                >
                                    <span class="font-bold text-xs sm:text-sm truncate w-full block" :class="selectedHora === '14:45' ? 'text-white' : ''">2:45 p.m.</span>
                                    <span class="text-[11px] sm:text-xs truncate w-full block mt-0.5" :class="selectedHora === '14:45' ? 'text-indigo-100' : ''">
                                        @if(!$horariosStatus['14:45']['habilitado'])
                                            Cerrado
                                        @else
                                            {{ $horariosStatus['14:45']['libres'] }} libres
                                        @endif
                                    </span>
                                </button>

                                <!-- 15:30 Button (Acceso libre) -->
                                <button
                                    type="button"
                                    @click="selectedHora = '15:30'"
                                    @if(!$horariosStatus['15:30']['habilitado']) disabled @endif
                                    :class="selectedHora === '15:30' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md ring-2 ring-emerald-300' : (@json(!$horariosStatus['15:30']['habilitado']) ? 'bg-gray-100/90 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100')"
                                    style="flex: 1 1 0%; min-width: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 12px 4px; border-radius: 16px; border-width: 1px; border-style: solid; text-align: center; transition: all 0.2s; min-height: 66px;"
                                >
                                    <span class="font-bold text-xs sm:text-sm truncate w-full block" :class="selectedHora === '15:30' ? 'text-white' : ''">3:30 p.m.</span>
                                    <span class="text-[11px] sm:text-xs truncate w-full block mt-0.5 {{ old('hora') === '15:30' ? 'text-emerald-100' : 'text-emerald-600 font-bold' }}" :class="selectedHora === '15:30' ? 'text-emerald-100' : ''">
                                        @if(!$horariosStatus['15:30']['habilitado'])
                                            <span class="text-emerald-600 font-semibold">Cerrado</span>
                                        @else
                                            Acceso libre
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
                <div class="md:col-span-7 w-full bg-white p-6 sm:p-8 lg:p-10 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-full">
                    <div>
                        <!-- Logotipo Comedor GILOU -->
                        <div class="flex flex-col mb-8 relative select-none">
                            <div class="relative inline-block">
                                <img src="{{ asset('images/logoguilles.jpeg') }}" alt="Comedor Gilou" class="w-44 sm:w-52 md:w-60 h-auto">
                            </div>
                        </div>

                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            Reserva tu lugar en el comedor
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed mb-8">
                            Para asegurar una mejor atención y coordinar el servicio de alimentos diariamente, por favor registre su reservación ingresando su número de colaborador y seleccionando el horario de su preferencia. Las reservaciones se realizan exclusivamente para el día de hoy.
                        </p>

                        <!-- Puntos de guía -->
                        <div class="space-y-4 text-sm text-gray-500">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span>5 Horarios Disponibles: <p>12:30 p.m. a 1:00 p.m., 1:15 p.m. a 1:45 p.m., 2:00 p.m. a 2:30 p.m., 2:45 p.m. a 3:15 p.m. y 3:30 p.m. a 4:00 p.m.</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span>Capacidad de 120 lugares por horario (Acceso libre de 3:30 p.m. a 4:00 p.m.).</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span>Límite de 1 reservación por día por colaborador.
                                    <p>Podrás reservar hasta 15 minutos antes de tu horario de comida, sujeto a disponibilidad.
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4h4m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />    
                                    </svg>
                                </div>
                                <span>
                                    El sistema de reservas estará disponible todos los días a partir de las 8:00 a.m.
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span>El colaborador debe estar registrado y activo en el sistema.</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />    
                                    </svg>
                                </div>
                                <span>Llega puntual dentro del horario que reservaste. Si no acudes en el horario seleccionado, tu QR quedará inhabilitado. En ese caso, únicamente podrás ingresar en el último horario disponible (3:30 p.m.)</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <span>
                                    La línea de servicio cerrará al concluir cada turno y permanecerá cerrada durante 15 minutos. Durante ese periodo no habrá acceso (o servicio)
                                </span>
                            </div>
          
                            
                        </div>
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
