<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
            {{ __('Encuesta de Satisfacción del Comedor') }}
        </h2>
    </x-slot>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-8 bg-gray-50/50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- PASO 1: VERIFICACIÓN DE COLABORADOR -->
            <div id="verification-card" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 transition-all duration-300">
                <div class="flex items-center gap-4 mb-6">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3 3 0 00-3 3h6a3 3 0 00-3-3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Validación de Comensal</h3>
                        <p class="text-sm text-gray-500">Ingresa tu número de empleado para habilitar la encuesta de satisfacción.</p>
                    </div>
                </div>

                <form id="form-validar-empleado" onsubmit="validarEmpleado(event)" class="space-y-4">
                    @csrf
                    <div>
                        <label for="numero_empleado" class="block text-sm font-semibold text-gray-700 mb-1">
                            Número de Empleado
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                id="numero_empleado"
                                name="numero_empleado"
                                required
                                autofocus
                                placeholder="Ej: 10452"
                                class="pl-10 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 font-mono"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        id="btn-validar"
                        class="w-full flex items-center justify-center gap-2 py-3.5 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-xl font-semibold text-base transition duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <span>Validar Datos y Continuar</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- PASO 2: FORMULARIO DE ENCUESTA (OCULTO INICIALMENTE) -->
            <div id="survey-card" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6 transition-all duration-300">
                <!-- Banner del Comensal Validado -->
                <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-indigo-600 text-white rounded-lg font-bold text-sm">
                            <span id="badge-numero-empleado">#</span>
                        </div>
                        <div>
                            <h4 id="badge-nombre-empleado" class="font-bold text-gray-900 text-base">Nombre</h4>
                            <p id="badge-depto-empleado" class="text-xs text-indigo-700 font-medium">Departamento</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-full w-fit">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Ingreso al comedor verificado hoy
                    </span>
                </div>

                <div class="border-b border-gray-100 pb-4">
                    <h3 class="text-xl font-bold text-gray-900">Evaluación de Servicio del Comedor</h3>
                    <p class="text-sm text-gray-500">Selecciona la calificación de 1 a 5 estrellas para cada uno de los criterios.</p>
                </div>

                <form id="form-encuesta" onsubmit="guardarEncuesta(event)" class="space-y-6">
                    @csrf
                    <input type="hidden" id="survey_empleado_id" name="empleado_id" value="" />

                    <!-- CRITERIO 1: Calidad de alimentos -->
                    <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:border-indigo-100 transition duration-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <div>
                                <h4 class="font-bold text-gray-800 text-base">1. Calidad de alimentos</h4>
                                <p class="text-xs text-gray-500">Sabor, frescura y variedad de platillos</p>
                            </div>
                            <span id="label-calidad_alimentos" class="text-xs font-semibold text-gray-400">Sin calificar</span>
                        </div>
                        <div class="star-rating flex items-center gap-2" data-name="calidad_alimentos">
                            <!-- 5 estrellas -->
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" onclick="setRating('calidad_alimentos', {{ $i }})" onmouseenter="hoverRating('calidad_alimentos', {{ $i }})" onmouseleave="resetHover('calidad_alimentos')" class="star-btn p-1 text-gray-300 hover:text-amber-400 transition duration-150 focus:outline-none" data-value="{{ $i }}">
                                    <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                </button>
                            @endfor
                            <input type="hidden" name="calidad_alimentos" id="input-calidad_alimentos" value="" required />
                        </div>
                    </div>

                    <!-- CRITERIO 2: Limpieza e higiene -->
                    <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:border-indigo-100 transition duration-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <div>
                                <h4 class="font-bold text-gray-800 text-base">2. Limpieza e higiene</h4>
                                <p class="text-xs text-gray-500">Instalaciones, utensilios y manipulación de alimentos</p>
                            </div>
                            <span id="label-limpieza_higiene" class="text-xs font-semibold text-gray-400">Sin calificar</span>
                        </div>
                        <div class="star-rating flex items-center gap-2" data-name="limpieza_higiene">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" onclick="setRating('limpieza_higiene', {{ $i }})" onmouseenter="hoverRating('limpieza_higiene', {{ $i }})" onmouseleave="resetHover('limpieza_higiene')" class="star-btn p-1 text-gray-300 hover:text-amber-400 transition duration-150 focus:outline-none" data-value="{{ $i }}">
                                    <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                </button>
                            @endfor
                            <input type="hidden" name="limpieza_higiene" id="input-limpieza_higiene" value="" required />
                        </div>
                    </div>

                    <!-- CRITERIO 3: Temperatura adecuada -->
                    <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:border-indigo-100 transition duration-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <div>
                                <h4 class="font-bold text-gray-800 text-base">3. Temperatura adecuada</h4>
                                <p class="text-xs text-gray-500">Alimentos calientes (≥60°C) y fríos (≤7°C)</p>
                            </div>
                            <span id="label-temperatura_adecuada" class="text-xs font-semibold text-gray-400">Sin calificar</span>
                        </div>
                        <div class="star-rating flex items-center gap-2" data-name="temperatura_adecuada">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" onclick="setRating('temperatura_adecuada', {{ $i }})" onmouseenter="hoverRating('temperatura_adecuada', {{ $i }})" onmouseleave="resetHover('temperatura_adecuada')" class="star-btn p-1 text-gray-300 hover:text-amber-400 transition duration-150 focus:outline-none" data-value="{{ $i }}">
                                    <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                </button>
                            @endfor
                            <input type="hidden" name="temperatura_adecuada" id="input-temperatura_adecuada" value="" required />
                        </div>
                    </div>

                    <!-- CRITERIO 4: Atención y eficiencia -->
                    <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:border-indigo-100 transition duration-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <div>
                                <h4 class="font-bold text-gray-800 text-base">4. Atención y eficiencia</h4>
                                <p class="text-xs text-gray-500">Tiempo de espera en fila y trato del personal</p>
                            </div>
                            <span id="label-atencion_eficiencia" class="text-xs font-semibold text-gray-400">Sin calificar</span>
                        </div>
                        <div class="star-rating flex items-center gap-2" data-name="atencion_eficiencia">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" onclick="setRating('atencion_eficiencia', {{ $i }})" onmouseenter="hoverRating('atencion_eficiencia', {{ $i }})" onmouseleave="resetHover('atencion_eficiencia')" class="star-btn p-1 text-gray-300 hover:text-amber-400 transition duration-150 focus:outline-none" data-value="{{ $i }}">
                                    <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                </button>
                            @endfor
                            <input type="hidden" name="atencion_eficiencia" id="input-atencion_eficiencia" value="" required />
                        </div>
                    </div>

                    <!-- CRITERIO 5: Presentación -->
                    <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:border-indigo-100 transition duration-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <div>
                                <h4 class="font-bold text-gray-800 text-base">5. Presentación</h4>
                                <p class="text-xs text-gray-500">Presentación en línea fría y línea caliente</p>
                            </div>
                            <span id="label-presentacion" class="text-xs font-semibold text-gray-400">Sin calificar</span>
                        </div>
                        <div class="star-rating flex items-center gap-2" data-name="presentacion">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" onclick="setRating('presentacion', {{ $i }})" onmouseenter="hoverRating('presentacion', {{ $i }})" onmouseleave="resetHover('presentacion')" class="star-btn p-1 text-gray-300 hover:text-amber-400 transition duration-150 focus:outline-none" data-value="{{ $i }}">
                                    <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                </button>
                            @endfor
                            <input type="hidden" name="presentacion" id="input-presentacion" value="" required />
                        </div>
                    </div>

                    <!-- COMENTARIOS OPCIONALES -->
                    <div>
                        <label for="comentarios" class="block text-sm font-semibold text-gray-700 mb-1">
                            Comentarios o sugerencias adicionales (Opcional)
                        </label>
                        <textarea
                            id="comentarios"
                            name="comentarios"
                            rows="3"
                            placeholder="Escribe aquí cualquier sugerencia para ayudarnos a mejorar..."
                            class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3"
                        ></textarea>
                    </div>

                    <!-- BOTÓN ENVIAR -->
                    <button
                        type="submit"
                        id="btn-guardar"
                        class="w-full flex items-center justify-center gap-2 py-4 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl font-bold text-base transition duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Enviar Encuesta de Satisfacción</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- JAVASCRIPT LÓGICA DE INTERACCIÓN, STAR RATING Y AJAX -->
    <script>
        const ratingsState = {
            calidad_alimentos: 0,
            limpieza_higiene: 0,
            temperatura_adecuada: 0,
            atencion_eficiencia: 0,
            presentacion: 0
        };

        const ratingLabels = {
            1: { text: "1 - Muy deficiente", color: "text-rose-600 font-bold" },
            2: { text: "2 - Deficiente", color: "text-orange-600 font-bold" },
            3: { text: "3 - Regular", color: "text-amber-600 font-bold" },
            4: { text: "4 - Bueno", color: "text-emerald-600 font-bold" },
            5: { text: "5 - Excelente", color: "text-indigo-600 font-bold" }
        };

        function hoverRating(name, val) {
            updateStarColors(name, val);
        }

        function resetHover(name) {
            updateStarColors(name, ratingsState[name]);
        }

        function setRating(name, val) {
            ratingsState[name] = val;
            document.getElementById(`input-${name}`).value = val;
            updateStarColors(name, val);

            const labelEl = document.getElementById(`label-${name}`);
            if (ratingLabels[val]) {
                labelEl.innerText = ratingLabels[val].text;
                labelEl.className = `text-xs ${ratingLabels[val].color}`;
            }
        }

        function updateStarColors(name, activeVal) {
            const container = document.querySelector(`.star-rating[data-name="${name}"]`);
            if (!container) return;

            const buttons = container.querySelectorAll('.star-btn');
            buttons.forEach(btn => {
                const btnVal = parseInt(btn.getAttribute('data-value'));
                if (btnVal <= activeVal) {
                    btn.classList.remove('text-gray-300');
                    btn.classList.add('text-amber-400');
                } else {
                    btn.classList.remove('text-amber-400');
                    btn.classList.add('text-gray-300');
                }
            });
        }

        async function validarEmpleado(event) {
            event.preventDefault();
            const btn = document.getElementById('btn-validar');
            const numInput = document.getElementById('numero_empleado');
            const num = numInput.value.trim();

            if (!num) return;

            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const response = await fetch("{{ route('encuestas.validar') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ numero_empleado: num })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    Swal.fire({
                        icon: data.type || 'error',
                        title: data.title || 'Atención',
                        text: data.message || 'No fue posible validar tus datos.',
                        confirmButtonColor: '#4F46E5',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }

                // Éxito en validación
                Swal.fire({
                    icon: 'success',
                    title: data.title,
                    text: data.message,
                    timer: 2500,
                    showConfirmButton: false
                });

                // Llenar datos del comensal y habilitar formulario
                document.getElementById('survey_empleado_id').value = data.empleado.id;
                document.getElementById('badge-numero-empleado').innerText = `#${data.empleado.numero_empleado}`;
                document.getElementById('badge-nombre-empleado').innerText = data.empleado.nombre;
                document.getElementById('badge-depto-empleado').innerText = `${data.empleado.departamento} • ${data.empleado.puesto}`;

                document.getElementById('verification-card').classList.add('hidden');
                document.getElementById('survey-card').classList.remove('hidden');

            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'Ocurrió un error al intentar validar los datos. Por favor reintenta.',
                    confirmButtonColor: '#4F46E5'
                });
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        }

        async function guardarEncuesta(event) {
            event.preventDefault();

            // Validar que los 5 criterios tengan calificación
            const missing = Object.keys(ratingsState).filter(k => ratingsState[k] < 1);
            if (missing.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Calificaciones Incompletas',
                    text: 'Por favor asigna una calificación de 1 a 5 estrellas a todos los criterios.',
                    confirmButtonColor: '#4F46E5'
                });
                return;
            }

            const btn = document.getElementById('btn-guardar');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            const payload = {
                empleado_id: document.getElementById('survey_empleado_id').value,
                calidad_alimentos: ratingsState.calidad_alimentos,
                limpieza_higiene: ratingsState.limpieza_higiene,
                temperatura_adecuada: ratingsState.temperatura_adecuada,
                atencion_eficiencia: ratingsState.atencion_eficiencia,
                presentacion: ratingsState.presentacion,
                comentarios: document.getElementById('comentarios').value.trim()
            };

            try {
                const response = await fetch("{{ route('encuestas.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No fue posible registrar la encuesta',
                        text: data.message || 'Ocurrió un error inesperado al procesar tu solicitud.',
                        confirmButtonColor: '#4F46E5'
                    });
                    return;
                }

                await Swal.fire({
                    icon: 'success',
                    title: data.title,
                    text: data.message,
                    confirmButtonColor: '#059669',
                    confirmButtonText: 'Finalizar'
                });

                // Reiniciar vista
                location.reload();

            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Red',
                    text: 'No se pudo guardar la encuesta. Verifica tu conexión.',
                    confirmButtonColor: '#4F46E5'
                });
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        }
    </script>
</x-app-layout>
