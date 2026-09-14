/**
 * Módulo de selección de fechas para el flujo de reservaciones.
 * Controla la restricción condicional de fechas disponibles cuando
 * existen registros de Reserva Libre activa.
 */

export function initReservationDatePicker() {
    const container = document.getElementById('reservation-date-picker-container');
    if (!container) {
        return;
    }

    const inputFecha = document.getElementById('fecha_reservacion');
    const hasFreeBooking = container.dataset.hasFreeBooking === 'true';
    let allowedDates = [];

    try {
        allowedDates = JSON.parse(container.dataset.allowedDates || '[]');
    } catch (e) {
        console.error('Error al parsear fechas permitidas:', e);
    }

    const today = container.dataset.today;
    const currentSelected = inputFecha ? inputFecha.value : today;

    // Manejador para botones de cambio rápido de fecha (Hoy vs Reserva Libre)
    const dateButtons = container.querySelectorAll('[data-date-target]');
    dateButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const targetDate = this.dataset.dateTarget;

            if (targetDate === currentSelected) {
                return;
            }

            if (allowedDates.length > 0 && !allowedDates.includes(targetDate)) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Fecha no disponible',
                        text: 'Esta fecha no se encuentra autorizada para reservaciones.',
                        confirmButtonColor: '#4f46e5',
                    });
                }
                return;
            }

            // Cambiar parámetro en URL para actualizar cupos y disponibilidad del día seleccionado
            const url = new URL(window.location.href);
            url.searchParams.set('fecha', targetDate);
            window.location.href = url.toString();
        });
    });

    // Validación sobre input de tipo date si se encuentra presente
    if (inputFecha) {
        inputFecha.addEventListener('change', function () {
            const val = this.value;

            if (hasFreeBooking && allowedDates.length > 0 && !allowedDates.includes(val)) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fecha no autorizada',
                        text: 'Actualmente solo puede seleccionar el día de hoy o la fecha autorizada en Reserva Libre.',
                        confirmButtonColor: '#4f46e5',
                    });
                }
                this.value = currentSelected;
                return;
            }

            if (!hasFreeBooking && val !== today) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Fecha restringida',
                        text: 'En el flujo regular las reservaciones solo están habilitadas para el día de hoy.',
                        confirmButtonColor: '#4f46e5',
                    });
                }
                this.value = today;
                return;
            }

            if (val !== currentSelected) {
                const url = new URL(window.location.href);
                url.searchParams.set('fecha', val);
                window.location.href = url.toString();
            }
        });
    }
}

// Inicialización automática cuando el DOM esté listo
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReservationDatePicker);
    } else {
        initReservationDatePicker();
    }
}
