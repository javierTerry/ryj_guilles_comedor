<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Gestión de Reservas Libres') }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Habilita y administra fechas anticipadas para reservaciones en el comedor institucional.
                </p>
            </div>
            <div>
                <button type="button"
                    id="btn-agregar-reserva-libre"
                    data-testid="btn-agregar-reserva-libre"
                    onclick="abrirModalCrearReservaLibre()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Agregar Reserva Libre
                </button>
            </div>
        </div>
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
                    data-testid="tab-reservar"
                    class="px-4 py-2 text-sm font-semibold rounded-lg transition duration-150 {{ request()->routeIs('reservaciones.create') ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reservar
                    </span>
                </a>
                <a href="{{ route('reservaciones.cancel_view') }}"
                    data-testid="tab-cancelar"
                    class="px-4 py-2 text-sm font-semibold rounded-lg transition duration-150 {{ request()->routeIs('reservaciones.cancel_view') ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Cancelar
                    </span>
                </a>
                @can('manage-free-bookings')
                <a href="{{ route('reservas.libres.index') }}"
                    data-testid="tab-reserva-libre"
                    class="px-4 py-2 text-sm font-semibold rounded-lg transition duration-150 {{ request()->routeIs('reservas.libres.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Reserva Libre
                    </span>
                </a>
                @endcan
            </div>

            <!-- TARJETA PRINCIPAL Y TABLA DE SEGUIMIENTO -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-800">
                            Fechas Habilitadas para Reserva Libre
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                            Listado histórico y estatus operativo de las fechas autorizadas para reservas anticipadas.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Activo
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Aplicado
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Cancelado
                        </span>
                    </div>
                </div>

                <!-- TABLA RESPONSIVA -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 font-semibold text-gray-700 uppercase tracking-wider text-xs">
                                    Fecha Reserva Libre
                                </th>
                                <th scope="col" class="px-6 py-3.5 font-semibold text-gray-700 uppercase tracking-wider text-xs">
                                    Creado por
                                </th>
                                <th scope="col" class="px-6 py-3.5 font-semibold text-gray-700 uppercase tracking-wider text-xs">
                                    Estatus
                                </th>
                                <th scope="col" class="px-6 py-3.5 font-semibold text-gray-700 uppercase tracking-wider text-xs text-right">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white" id="tabla-reservas-libres" data-testid="tabla-reservas-libres">
                            @forelse ($bookings as $booking)
                                <tr class="hover:bg-gray-50/70 transition duration-150" id="fila-reserva-{{ $booking->id }}" data-testid="fila-reserva-{{ $booking->id }}">
                                    <!-- Fecha Reserva Libre -->
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-semibold" data-testid="fecha-reserva-{{ $booking->id }}">
                                                {{ $booking->booking_date->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Creado por -->
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                                {{ substr($booking->createdBy?->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800 text-sm leading-none" data-testid="creador-reserva-{{ $booking->id }}">
                                                    {{ $booking->createdBy?->name ?? 'Usuario no disponible' }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-0.5">
                                                    {{ $booking->created_at->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Estatus con Badges Visuales -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($booking->status === 'activo')
                                            <span data-testid="badge-status-{{ $booking->id }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Activo
                                            </span>
                                        @elseif ($booking->status === 'aplicado')
                                            <span data-testid="badge-status-{{ $booking->id }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                Aplicado
                                            </span>
                                        @elseif ($booking->status === 'cancelado')
                                            <span data-testid="badge-status-{{ $booking->id }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Cancelado
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if ($booking->status === 'activo')
                                            <button type="button"
                                                data-testid="btn-cancelar-{{ $booking->id }}"
                                                onclick="cambiarEstatus({{ $booking->id }}, 'cancelado', '{{ $booking->booking_date->format('d/m/Y') }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition duration-150 focus:outline-none focus:ring-2 focus:ring-rose-500"
                                                title="Cancelar esta reserva libre">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Cancelar
                                            </button>
                                        @elseif ($booking->status === 'cancelado')
                                            <button type="button"
                                                data-testid="btn-reactivar-{{ $booking->id }}"
                                                onclick="cambiarEstatus({{ $booking->id }}, 'activo', '{{ $booking->booking_date->format('d/m/Y') }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                                title="Reactivar esta reserva libre">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Reactivar
                                            </button>
                                        @elseif ($booking->status === 'aplicado')
                                            <span class="text-gray-400 font-medium select-none">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-base font-semibold text-gray-700">No hay reservas libres registradas</p>
                                            <p class="text-xs text-gray-400 mt-1 max-w-sm">
                                                Presiona el botón "Agregar Reserva Libre" para habilitar una fecha de reserva anticipada en el comedor.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN -->
                @if ($bookings->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- SCRIPTS JAVASCRIPT Y SWEETALERT2 -->
    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const RUTA_STORE = '{{ route("reservas.libres.store") }}';
        const RUTA_STATUS_TEMPLATE = '{{ route("reservas.libres.update_status", ["freeBooking" => ":id"]) }}';

        /**
         * Despliega modal SweetAlert2 con input: 'date' para registrar nueva Reserva Libre.
         */
        async function abrirModalCrearReservaLibre() {
            const todayStr = new Date().toISOString().split('T')[0];

            const { value: selectedDate } = await Swal.fire({
                title: 'Nueva Reserva Libre',
                text: 'Selecciona la fecha que deseas habilitar para reservaciones anticipadas:',
                input: 'date',
                inputLabel: 'Fecha de Reserva Libre',
                inputPlaceholder: 'AAAA-MM-DD',
                inputValue: todayStr,
                inputAttributes: {
                    min: todayStr,
                    required: 'required',
                    class: 'swal2-input border-gray-300 rounded-lg'
                },
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Habilitar Fecha',
                cancelButtonText: 'Cancelar',
                showLoaderOnConfirm: true,
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debes seleccionar una fecha obligatoria.';
                    }
                    if (value < todayStr) {
                        return 'La fecha debe ser igual o posterior al día de hoy.';
                    }
                    return null;
                },
                preConfirm: async (dateValue) => {
                    try {
                        const response = await fetch(RUTA_STORE, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': CSRF_TOKEN
                            },
                            body: JSON.stringify({ booking_date: dateValue })
                        });

                        let data;
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            data = await response.json();
                        } else {
                            Swal.showValidationMessage(`Respuesta no válida del servidor (${response.status}).`);
                            return false;
                        }

                        if (!response.ok) {
                            const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Ocurrió un error al procesar la solicitud.');
                            Swal.showValidationMessage(errorMsg);
                            return false;
                        }

                        return data;
                    } catch (error) {
                        Swal.showValidationMessage(`Error de comunicación: ${error.message}`);
                        return false;
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            });

            if (selectedDate && selectedDate.success) {
                await Swal.fire({
                    icon: 'success',
                    title: '¡Reserva Libre Creada!',
                    text: selectedDate.message || 'La fecha fue habilitada con éxito.',
                    confirmButtonColor: '#4f46e5',
                    timer: 2000,
                    timerProgressBar: true
                });
                window.location.reload();
            }
        }

        /**
         * Solicita confirmación previa para cancelar o reactivar una Reserva Libre.
         */
        async function cambiarEstatus(bookingId, nuevoEstatus, fechaFormateada) {
            const esCancelar = nuevoEstatus === 'cancelado';

            const configAlert = esCancelar ? {
                title: '¿Cancelar Reserva Libre?',
                text: `¿Estás seguro de cancelar la fecha ${fechaFormateada}? Los usuarios no podrán realizar reservaciones para este día.`,
                icon: 'warning',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Sí, cancelar reserva',
            } : {
                title: '¿Reactivar Reserva Libre?',
                text: `¿Deseas reactivar la reserva libre para la fecha ${fechaFormateada}?`,
                icon: 'question',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Sí, reactivar fecha',
            };

            const result = await Swal.fire({
                title: configAlert.title,
                text: configAlert.text,
                icon: configAlert.icon,
                showCancelButton: true,
                cancelButtonColor: '#9ca3af',
                confirmButtonColor: configAlert.confirmButtonColor,
                confirmButtonText: configAlert.confirmButtonText,
                cancelButtonText: 'Volver'
            });

            if (!result.isConfirmed) {
                return;
            }

            // Mostrar estado de carga
            Swal.fire({
                title: 'Actualizando estatus...',
                text: 'Por favor espera un momento.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const url = RUTA_STATUS_TEMPLATE.replace(':id', bookingId);
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({ status: nuevoEstatus })
                });

                let data;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    throw new Error(`El servidor devolvió un formato no válido (Código ${response.status}).`);
                }

                if (!response.ok) {
                    const errorMsg = data.message || 'No fue posible actualizar el estatus de la reserva libre.';
                    await Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg,
                        confirmButtonColor: '#4f46e5'
                    });
                    return;
                }

                await Swal.fire({
                    icon: 'success',
                    title: '¡Estatus Actualizado!',
                    text: data.message || 'La acción se completó con éxito.',
                    confirmButtonColor: '#4f46e5',
                    timer: 1800,
                    timerProgressBar: true
                });

                window.location.reload();
            } catch (error) {
                await Swal.fire({
                    icon: 'error',
                    title: 'Error de Red o Servidor',
                    text: `No se pudo completar la operación: ${error.message}`,
                    confirmButtonColor: '#4f46e5'
                });
            }
        }
    </script>
</x-app-layout>
