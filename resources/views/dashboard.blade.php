<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Dashboard de Estadísticas') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Métricas de consumo e indicadores en tiempo real</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden md:inline-flex px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full border border-indigo-100">
                    Actualizado en tiempo real
                </span>

                <!-- BOTÓN DESCARGAR PDF -->
                <button
                    type="button"
                    onclick="downloadDashboardPDF()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-semibold rounded-xl shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                    title="Descargar el Dashboard con las gráficas actuales en PDF"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Descargar PDF</span>
                </button>

                <!-- BOTÓN ENVIAR POR CORREO -->
                <button
                    type="button"
                    onclick="sendDashboardReportEmail()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-semibold rounded-xl shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                    title="Enviar o programar el reporte del dashboard por correo electrónico"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Enviar por Correo</span>
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Chart.js & html2pdf.js CDNs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-8">
        <div id="dashboard-report-content" class="w-full max-w-[95%] lg:max-w-[90%] mx-auto sm:px-6 lg:px-8 space-y-8 bg-gray-50 p-4 rounded-2xl">
            
            <!-- HEADER DE IMPRESIÓN / REPORTE -->
            <div class="hidden print:block pb-4 border-b border-gray-200 mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Reporte Estadístico de Comedor GILOU</h1>
                <p class="text-xs text-gray-500">Fecha de emisión: {{ now()->translatedFormat('d \d\e F \d\e Y - H:i') }} hrs</p>
            </div>

            <!-- KPI CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- CARD 1: Total Empleados -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Total Empleados</p>
                            <h3 class="text-3xl font-extrabold text-gray-800 mt-2">{{ $totalEmpleados }}</h3>
                        </div>
                        <div class="p-3 rounded-lg bg-blue-50 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-gray-500">
                        <span class="font-semibold text-emerald-600 mr-1">{{ $empleadosActivos }} activos</span>
                        <span>y {{ $empleadosInactivos }} inactivos</span>
                    </div>
                </div>

                <!-- CARD 2: Accesos Hoy -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Comidas Hoy</p>
                            <h3 class="text-3xl font-extrabold text-gray-800 mt-2">{{ $accesosHoy }}</h3>
                        </div>
                        <div class="p-3 rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-gray-500">
                        <span class="text-indigo-600 font-semibold">Registro diario actual</span>
                    </div>
                </div>

                <!-- CARD 3: Accesos Mes -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Comidas este Mes</p>
                            <h3 class="text-3xl font-extrabold text-gray-800 mt-2">{{ $accesosMes }}</h3>
                        </div>
                        <div class="p-3 rounded-lg bg-violet-50 text-violet-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-gray-500">
                        <span class="text-indigo-600 font-semibold">Mes en curso</span>
                    </div>
                </div>

                <!-- CARD 4: Promedio Diario (Estimado) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Promedio Diario</p>
                            <h3 class="text-3xl font-extrabold text-gray-800 mt-2">
                                {{ $accesosMes > 0 ? round($accesosMes / Carbon\Carbon::now()->day, 1) : 0 }}
                            </h3>
                        </div>
                        <div class="p-3 rounded-lg bg-amber-50 text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-gray-500">
                        <span class="text-indigo-600 font-semibold">Media estimada del mes</span>
                    </div>
                </div>

            </div>

            <!-- CHARTS ROW 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Chart 1: Daily Accesses -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span class="w-3 h-3 bg-indigo-500 rounded-full mr-2"></span>
                        Accesos Diarios (Últimos 15 días)
                    </h3>
                    <div class="relative" style="height: 320px;">
                        <canvas id="dailyChart"></canvas>
                    </div>
                </div>

                <!-- Chart 2: Hourly Distribution -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span class="w-3 h-3 bg-violet-500 rounded-full mr-2"></span>
                        Distribución de Horarios (Hora Pico)
                    </h3>
                    <div class="relative" style="height: 320px;">
                        <canvas id="hourlyChart"></canvas>
                    </div>
                </div>

            </div>

            <!-- CHARTS ROW 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Chart 3: Monthly Accesses -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span class="w-3 h-3 bg-emerald-500 rounded-full mr-2"></span>
                        Accesos Mensuales (Año Actual)
                    </h3>
                    <div class="relative" style="height: 320px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <!-- Chart 4: Department Breakdown -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span class="w-3 h-3 bg-amber-500 rounded-full mr-2"></span>
                        Accesos por Departamento
                    </h3>
                    <div class="relative flex items-center justify-center" style="height: 320px;">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Chart configurations and export functions script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Colors configuration
            const colors = {
                indigo: 'rgba(99, 102, 241, 1)',
                indigoLight: 'rgba(99, 102, 241, 0.1)',
                violet: 'rgba(139, 92, 246, 1)',
                violetLight: 'rgba(139, 92, 246, 0.2)',
                emerald: 'rgba(16, 185, 129, 1)',
                emeraldLight: 'rgba(16, 185, 129, 0.2)',
                amber: 'rgba(245, 158, 11, 1)',
                amberLight: 'rgba(245, 158, 11, 0.2)',
                rose: 'rgba(244, 63, 94, 1)',
                cyan: 'rgba(6, 182, 212, 1)',
                slate: 'rgba(100, 116, 139, 1)'
            };

            // 1. DAILY ACCESSES CHART
            const ctxDaily = document.getElementById('dailyChart').getContext('2d');
            const dailyGradient = ctxDaily.createLinearGradient(0, 0, 0, 300);
            dailyGradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
            dailyGradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

            new Chart(ctxDaily, {
                type: 'line',
                data: {
                    labels: @json($dailyLabels),
                    datasets: [{
                        label: 'Accesos',
                        data: @json($dailyValues),
                        borderColor: colors.indigo,
                        borderWidth: 3,
                        backgroundColor: dailyGradient,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: colors.indigo,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: colors.indigo,
                        pointHoverBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(243, 244, 246, 1)' },
                            ticks: { precision: 0 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // 2. HOURLY DISTRIBUTION CHART
            const ctxHourly = document.getElementById('hourlyChart').getContext('2d');
            new Chart(ctxHourly, {
                type: 'bar',
                data: {
                    labels: @json($hourlyLabels),
                    datasets: [{
                        label: 'Accesos',
                        data: @json($hourlyValues),
                        backgroundColor: colors.violet,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(124, 58, 237, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(243, 244, 246, 1)' },
                            ticks: { precision: 0 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // 3. MONTHLY ACCESSES CHART
            const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
            new Chart(ctxMonthly, {
                type: 'bar',
                data: {
                    labels: @json($monthlyLabels),
                    datasets: [{
                        label: 'Accesos por Mes',
                        data: @json($monthlyValues),
                        backgroundColor: colors.emerald,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(5, 150, 105, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(243, 244, 246, 1)' },
                            ticks: { precision: 0 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // 4. DEPARTMENT BREAKDOWN CHART
            const ctxDept = document.getElementById('deptChart').getContext('2d');
            const deptValues = @json($deptValues);
            
            if (deptValues.length === 0) {
                ctxDept.font = "14px Figtree, sans-serif";
                ctxDept.fillStyle = "#9ca3af";
                ctxDept.textAlign = "center";
                ctxDept.fillText("No hay datos de departamentos registrados", ctxDept.canvas.width / 2, ctxDept.canvas.height / 2);
            } else {
                new Chart(ctxDept, {
                    type: 'doughnut',
                    data: {
                        labels: @json($deptLabels),
                        datasets: [{
                            data: deptValues,
                            backgroundColor: [
                                colors.indigo,
                                colors.violet,
                                colors.emerald,
                                colors.amber,
                                colors.rose,
                                colors.cyan,
                                colors.slate
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    font: { size: 11, family: 'Figtree' },
                                    padding: 12
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        });

        /**
         * Download Dashboard as PDF with high-resolution charts
         */
        function downloadDashboardPDF() {
            Swal.fire({
                title: 'Generando PDF...',
                text: 'Capturando las gráficas e indicadores en tiempo real.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const element = document.getElementById('dashboard-report-content');
            const options = {
                margin:       [8, 8, 8, 8],
                filename:     'Reporte_Dashboard_Comedor_' + new Date().toISOString().slice(0, 10) + '.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, logging: false },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };

            html2pdf().set(options).from(element).save().then(() => {
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: '¡PDF Generado!',
                    text: 'El reporte en PDF del dashboard ha sido descargado correctamente.',
                    timer: 2500,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-2xl border border-gray-100' }
                });
            }).catch(err => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error al generar PDF',
                    text: 'Ocurrió un inconveniente al construir el archivo PDF: ' + err.message,
                    confirmButtonColor: '#4f46e5'
                });
            });
        }

        /**
         * Open modal to send dashboard report via email or schedule recurrent dispatch
         */
        function sendDashboardReportEmail() {
            const userEmail = "{{ auth()->user()->email ?? '' }}";

            Swal.fire({
                title: 'Enviar / Programar Reporte',
                html: `
                    <div class="text-left space-y-4 text-sm mt-2">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Correo electrónico del destinatario *</label>
                            <input id="swal-email" type="email" value="${userEmail}" placeholder="admin@empresa.com" class="w-full px-3 py-2 border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Frecuencia del reporte</label>
                            <select id="swal-periodo" class="w-full px-3 py-2 border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="Diario" selected>Envío inmediato / Resumen Diario</option>
                                <option value="Semanal">Programación Semanal (Lunes)</option>
                                <option value="Mensual">Programación Mensual (Día 1)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Comentario o nota (Opcional)</label>
                            <textarea id="swal-notas" rows="2" placeholder="Agregar notas sobre las métricas..." class="w-full px-3 py-2 border border-gray-300 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Enviar Reporte',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6b7280',
                customClass: { popup: 'rounded-2xl border border-gray-100' },
                preConfirm: () => {
                    const email = document.getElementById('swal-email').value.trim();
                    const periodo = document.getElementById('swal-periodo').value;
                    const notas = document.getElementById('swal-notas').value.trim();

                    if (!email) {
                        Swal.showValidationMessage('El correo electrónico es obligatorio');
                        return false;
                    }
                    return { email, periodo, notas };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = result.value;

                    Swal.fire({
                        title: 'Enviando correo...',
                        text: 'Procesando el envío del reporte a ' + data.email,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch("{{ route('dashboard.send_email') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(response => {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Reporte Enviado!',
                                text: response.message,
                                confirmButtonColor: '#059669',
                                customClass: { popup: 'rounded-2xl border border-gray-100' }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'No se pudo enviar',
                                text: response.message || 'Ocurrió un fallo al enviar el reporte.',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de red',
                            text: 'No se pudo establecer contacto con el servidor: ' + err.message,
                            confirmButtonColor: '#ef4444'
                        });
                    });
                }
            });
        }
    </script>
</x-app-layout>
