<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reservación de Comedor') }}
        </h2>
    </x-slot>

    <!-- Estilos de fuentes e identidad visual -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Playfair+Display:ital,wght@0,700;1,700&family=Outfit:wght@400;500;600;700&display=swap');
        
        .font-brand-logo-cursive {
            font-family: 'Dancing Script', cursive;
        }
        .font-brand-logo-serif {
            font-family: 'Playfair Display', serif;
        }
        .font-app-outfit {
            font-family: 'Outfit', sans-serif;
        }
    </style>

    <div class="py-12 bg-slate-50 min-h-screen font-app-outfit">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch animate-fade-in">
                
            <!-- TARJETA DEL FORMULARIO DE RESERVACIONES -->
                <div class="lg:col-span-7 bg-white p-8 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-center">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6">
                        Haz tu reservación
                    </h2>

                    <form action="{{ route('reservaciones.store') }}" method="POST" class="space-y-6" x-data="{ 
                        selectedHora: '{{ old('hora', '') }}'
                    }" @submit="if(!selectedHora) { Swal.fire({ icon: 'warning', title: 'Horario requerido', text: 'Por favor, elija uno de los tres horarios disponibles haciendo clic sobre él.', confirmButtonColor: '#4f46e5' }); $event.preventDefault(); }">
                        @csrf

                        <!-- NÚMERO DE COLABORADOR -->
                        <div class="space-y-2">
                            <label for="numero_empleado" class="block text-sm font-semibold text-slate-700">
                                Número de colaborador
                            </label>
                            <div class="flex rounded-xl border-2 border-indigo-100 bg-slate-50 overflow-hidden focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-150 transition duration-200">
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
                                    class="border-0 bg-transparent flex-1 block w-full text-slate-700 placeholder-slate-400 font-medium py-3 px-2 focus:ring-0 focus:outline-none"
                                />
                            </div>
                        </div>

                        <!-- FECHA -->
                        <div class="space-y-2">
                            <label for="fecha" class="block text-sm font-semibold text-slate-700">
                                Selecciona una fecha
                            </label>
                            <div class="flex rounded-xl border-2 border-indigo-100 bg-slate-50 overflow-hidden focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-150 transition duration-200">
                                <span class="inline-flex items-center pl-4 pr-2 text-indigo-400">
                                    <!-- Calendar Icon -->
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <input
                                    type="date"
                                    name="fecha"
                                    id="fecha"
                                    min="{{ date('Y-m-d') }}"
                                    value="{{ old('fecha', date('Y-m-d')) }}"
                                    required
                                    class="border-0 bg-transparent flex-1 block w-full text-slate-700 font-medium py-3 px-2 focus:ring-0 focus:outline-none"
                                />
                            </div>
                        </div>

                        <!-- Campo oculto para enviar la hora seleccionada -->
                        <input type="hidden" name="hora" :value="selectedHora" required />

                        <!-- HORA BUTTONS SECTION -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-slate-700">
                                Selección rápida de hora
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                <!-- 12:30 Button -->
                                <button
                                    type="button"
                                    @click="selectedHora = '12:30'"
                                    :class="selectedHora === '12:30' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                    class="py-3.5 px-1 border rounded-xl font-semibold text-sm text-center transition duration-200 focus:outline-none"
                                >
                                    12:30 p.m.
                                </button>

                                <!-- 13:45 Button -->
                                <button
                                    type="button"
                                    @click="selectedHora = '13:45'"
                                    :class="selectedHora === '13:45' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                    class="py-3.5 px-1 border rounded-xl font-semibold text-sm text-center transition duration-200 focus:outline-none"
                                >
                                    13:45 p.m.
                                </button>

                                <!-- 14:45 Button -->
                                <button
                                    type="button"
                                    @click="selectedHora = '14:45'"
                                    :class="selectedHora === '14:45' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                    class="py-3.5 px-1 border rounded-xl font-semibold text-sm text-center transition duration-200 focus:outline-none"
                                >
                                    14:45 p.m.
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
                <div class="lg:col-span-5 bg-white p-8 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800 mb-4">
                            Reserva tu lugar en el comedor
                        </h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-8">
                            Para asegurar una mejor atención y coordinar el servicio de alimentos diariamente, por favor registre su reservación ingresando su número de colaborador, la fecha y el horario de su preferencia.
                        </p>

                        <!-- Puntos de guía -->
                        <div class="space-y-4 text-xs text-slate-500">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span>3 Horarios Disponibles: 12:30 p.m., 13:45 p.m. y 14:45 p.m.</span>
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
                    <div class="mt-8 pt-4 border-t border-slate-100 text-xs text-slate-400">
                        © {{ date('Y') }} Comedor GILOU. Todos los derechos reservados.
                    </div>
                </div>

                

            </div>
        </div>
    </div>
</x-app-layout>
