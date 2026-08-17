<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cancelar Reservación de Comedor') }}
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

            <!-- SUBMENÚ DE NAVEGACIÓN DE RESERVACIONES -->
            <div class="flex items-center space-x-2 border-b border-gray-200 mb-6 pb-2">
                <a href="{{ route('reservaciones.create') }}"
                    class="px-4 py-2 text-sm font-semibold rounded-lg transition duration-150 {{ request()->routeIs('reservaciones.create') ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reservar
                    </span>
                </a>
                <a href="{{ route('reservaciones.cancel_view') }}"
                    class="px-4 py-2 text-sm font-semibold rounded-lg transition duration-150 {{ request()->routeIs('reservaciones.cancel_view') ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Cancelar
                    </span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8 items-stretch">

                <!-- TARJETA DEL FORMULARIO DE CANCELACIÓN (PANEL CANCELACION) -->
                <div id="panel-cancelacion"
                    class="md:col-span-5 w-full bg-white p-6 sm:p-8 lg:p-10 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-start h-full">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">
                        Cancelar reservación
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 mb-6">
                        Ingrese su número de colaborador y correo registrado para buscar su reservación activa.
                    </p>

                    <!-- Formulario de búsqueda para cancelación -->
                    <form id="cancelacion-form" action="{{ route('reservaciones.cancel_store') }}" method="POST"
                        class="space-y-6" onsubmit="buscarReservacionParaCancelar(event)">
                        @csrf

                        <!-- NÚMERO DE COLABORADOR -->
                        <div class="space-y-2">
                            <label for="numero_empleado_cancel" class="block text-sm font-semibold text-gray-700">
                                Número de colaborador
                            </label>
                            <div
                                class="flex rounded-xl border-2 border-indigo-100 bg-gray-50 overflow-hidden focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition duration-200">
                                <span class="inline-flex items-center pl-4 pr-2 text-indigo-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </span>
                                <input type="text" name="numero_empleado" id="numero_empleado_cancel"
                                    value="{{ old('numero_empleado') }}" placeholder="Ingrese su número de colaborador"
                                    maxlength="10" pattern="[0-9]{1,10}"
                                    title="Debe contener hasta 10 dígitos numéricos" required
                                    class="border-0 bg-transparent flex-1 block w-full text-gray-700 placeholder-gray-400 font-medium py-3 px-2 focus:ring-0 focus:outline-none" />
                            </div>
                        </div>

                        <!-- CORREO ELECTRÓNICO -->
                        <div class="space-y-2">
                            <label for="correo_cancel" class="block text-sm font-semibold text-gray-700">
                                Correo electrónico registrado
                            </label>
                            <div
                                class="flex rounded-xl border-2 border-indigo-100 bg-gray-50 overflow-hidden focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition duration-200">
                                <span class="inline-flex items-center pl-4 pr-2 text-indigo-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <input type="email" name="correo" id="correo_cancel" value="{{ old('correo') }}"
                                    placeholder="ejemplo@correo.com" required
                                    class="border-0 bg-transparent flex-1 block w-full text-gray-700 placeholder-gray-400 font-medium py-3 px-2 focus:ring-0 focus:outline-none" />
                            </div>
                        </div>

                        <!-- REGLA INFORMATIVA DE 30 MIN -->
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-start gap-2.5">
                            <svg class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <span class="font-bold">Regla de cancelación:</span>
                                <p class="mt-0.5">La reservación solo se podrá cancelar con al menos 30 minutos de anticipación al horario reservado previamente.</p>
                            </div>
                        </div>

                        <!-- BOTÓN BUSCAR Y CANCELAR -->
                        <div class="pt-2">
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 py-3.5 px-4 bg-rose-600 hover:bg-rose-700 active:bg-rose-900 text-white rounded-xl font-semibold text-base transition duration-200 focus:outline-none focus:ring-2 focus:ring-rose-500 shadow-sm">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Buscar reservación para cancelar
                            </button>
                        </div>

                    </form>
                </div>

                <!-- PANEL INFORMATIVO REUTILIZABLE -->
                @include('reservaciones.partials.panel-informativo')

            </div>
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación Exitosa!',
                    text: "{{ session('success') }}",
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#4f46e5',
                    customClass: {
                        popup: 'rounded-2xl border border-gray-100'
                    }
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo realizar la acción',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'rounded-2xl border border-gray-100'
                    }
                });
            });
        </script>
    @endif

    <script>
        function buscarReservacionParaCancelar(event) {
            event.preventDefault();
            const numEmp = document.getElementById('numero_empleado_cancel').value.trim();
            const correo = document.getElementById('correo_cancel').value.trim();

            if (!numEmp || !correo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'Ingrese su número de colaborador y correo electrónico.',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            Swal.fire({
                title: 'Buscando reservación...',
                text: 'Verificando datos del colaborador',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Endpoint AJAX para buscar la reservación activa
            fetch("{{ route('reservaciones.buscar_reservacion') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    numero_empleado: numEmp,
                    correo: correo
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sin Reservación Activa',
                        text: data.message || 'No se encontró ninguna reservación activa para el colaborador.',
                        confirmButtonColor: '#4f46e5',
                        customClass: {
                            popup: 'rounded-2xl border border-gray-100'
                        }
                    });
                    return;
                }

                // Desestructurar datos de la reservación encontrada
                const { empleado_nombre, hora_reservada, fecha_reservada, permite_cancelar } = data;

                if (!permite_cancelar) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tiempo límite excedido',
                        html: `
                            <p class="text-sm text-gray-600 mb-3">${data.message}</p>
                            <div class="p-3 bg-amber-50 text-amber-800 rounded-lg text-xs font-medium border border-amber-200">
                                La reservación de las <strong>${hora_reservada} p.m.</strong> requiere cancelarse con al menos 30 minutos de anticipación.
                            </div>
                        `,
                        confirmButtonColor: '#4f46e5',
                        customClass: {
                            popup: 'rounded-2xl border border-gray-100'
                        }
                    });
                    return;
                }

                // Modal interactivo de confirmación de cancelación
                Swal.fire({
                    title: '¿Confirmar Cancelación de Reservación?',
                    html: `
                        <div class="text-left mt-2 p-4 bg-gray-50 rounded-xl border border-gray-200 text-sm space-y-2">
                            <div><span class="font-bold text-gray-600">Colaborador:</span> <span class="text-gray-900 font-semibold">${empleado_nombre}</span></div>
                            <div><span class="font-bold text-gray-600">Fecha:</span> <span class="text-gray-900 font-semibold">${fecha_reservada}</span></div>
                            <div><span class="font-bold text-gray-600">Horario Reservado:</span> <span class="text-rose-600 font-bold bg-rose-50 px-2.5 py-1 rounded text-xs">${hora_reservada} p.m.</span></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-4 text-center">Al cancelar, se liberará tu lugar en el comedor y el registro se conservará para seguimiento y reportes.</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, Cancelar Reservación',
                    cancelButtonText: 'No, Conservar Reservación',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    customClass: {
                        popup: 'rounded-2xl border border-gray-100',
                        confirmButton: 'px-4 py-2 text-sm font-semibold rounded-lg',
                        cancelButton: 'px-4 py-2 text-sm font-semibold rounded-lg'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        ejecutarCancelacion(numEmp, correo);
                    }
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de servidor',
                    text: error.message || 'Ocurrió un problema de comunicación.',
                    confirmButtonColor: '#4f46e5'
                });
            });
        }

        function ejecutarCancelacion(numeroEmpleado, correo) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('reservaciones.cancel_store') }}";

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            const numInput = document.createElement('input');
            numInput.type = 'hidden';
            numInput.name = 'numero_empleado';
            numInput.value = numeroEmpleado;
            form.appendChild(numInput);

            const mailInput = document.createElement('input');
            mailInput.type = 'hidden';
            mailInput.name = 'correo';
            mailInput.value = correo;
            form.appendChild(mailInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</x-app-layout>
