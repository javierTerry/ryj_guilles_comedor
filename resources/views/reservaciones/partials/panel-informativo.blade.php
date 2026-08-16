<!-- PANEL INFORMATIVO Y LOGOTIPO (REUTILIZABLE) -->
<div id="panel-informativo"
    class="md:col-span-7 w-full bg-white p-6 sm:p-8 lg:p-10 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-full">
    <div>
        <!-- Logotipo Comedor GILOU -->
        <div class="flex flex-col mb-8 relative select-none">
            <div class="relative inline-block">
                <img src="{{ asset('images/logoguilles.jpeg') }}" alt="Comedor Gilou"
                    class="w-44 sm:w-52 md:w-60 h-auto">
            </div>
        </div>

        <h3 class="text-lg font-bold text-gray-800 mb-4">
            Reserva tu lugar en el comedor
        </h3>
        <p class="text-gray-600 text-sm leading-relaxed mb-8">
            Para asegurar una mejor atención y coordinar el servicio de alimentos diariamente, por favor
            registre su reservación ingresando su número de colaborador y seleccionando el horario de su
            preferencia. Las reservaciones se realizan exclusivamente para el día de hoy.
        </p>

        <!-- Puntos de guía -->
        <div class="space-y-4 text-sm text-gray-500">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span>5 Horarios Disponibles: 12:30 p.m. a 1:00 p.m., 1:15 p.m. a 1:45 p.m., 2:00 p.m. a
                    2:30 p.m., 2:45 p.m. a 3:15 p.m. y 3:30 p.m. a 4:30 p.m.</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span>Capacidad: 120 (12:30), 140 (1:15, 2:00 y 2:45) lugares por horario (Acceso libre
                    de 3:30 p.m. a 4:30 p.m.).</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span>Límite de 1 reservación por día por colaborador.
                    <p>Podrás reservar hasta 15 minutos antes de tu horario de comida, sujeto a
                        disponibilidad.
                </span>
            </div>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-rose-50 text-rose-600 rounded-lg shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span>Cancelaciones y Cambios: Podrás cancelar o cambiar tu horario al menos 30 minutos antes de la hora reservada.</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4h4m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                        <path fill-rule="evenodd"
                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <span>El colaborador debe estar registrado y activo en el sistema.</span>
            </div>

            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                </div>
                <span>Llega puntual dentro del horario que reservaste. Si no acudes en el horario
                    seleccionado, tu QR quedará inhabilitado. En ese caso, únicamente podrás ingresar en
                    el último horario disponible (3:30 p.m.)</span>
            </div>

            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <span>
                    La línea de servicio cerrará al concluir cada turno y permanecerá cerrada durante 15
                    minutos. Durante ese periodo no habrá acceso (o servicio)
                </span>
            </div>
        </div>
    </div>
    <br>
    <div>
        Agradecemos tu apoyo y colaboración para mantener un servicio más ágil, ordenado y eficiente
        para todos.
        Para dudas o soporte técnico, favor de contactar a Pamela Martínez.
    </div>
</div>
