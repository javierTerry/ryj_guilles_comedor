<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
                {{ __('Informe de Satisfacción del Usuario (ISU)') }}
            </h2>

            <!-- Acciones / Imprimir PDF (Visible solo en pantalla) -->
            <div class="print:hidden flex items-center gap-3">
                <button onclick="window.print()" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl font-semibold text-sm shadow-sm transition duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimir / Guardar PDF
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Estilos específicos para impresión PDF -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #documento-isu-pdf, #documento-isu-pdf * {
                visibility: visible;
            }
            #documento-isu-pdf {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none !important;
                border: none !important;
            }
            nav, header, footer {
                display: none !important;
            }
            .print\:hidden {
                display: none !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>

    <div class="py-8">
        <div class="w-full max-w-[90%] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- PESTAÑAS DE NAVEGACIÓN DE REPORTES (SOLO EN PANTALLA) -->
            <div class="print:hidden border-b border-gray-200 bg-white px-4 rounded-xl shadow-sm">
                <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                    <a href="{{ route('reportes.index') }}"
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 00-2 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Reporte General (Resumen)
                    </a>
                    <a href="{{ route('reportes.visitas') }}"
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Reporte de Visitas (Detalle e Ingresos)
                    </a>
                    <a href="{{ route('reportes.reservas') }}"
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reporte de Reservas (Día / Horarios)
                    </a>
                    <a href="{{ route('reportes.encuestas') }}"
                       class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        Reporte de Encuestas (Satisfacción)
                    </a>
                    <a href="{{ route('reportes.isu') }}"
                       class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2"
                       aria-current="page">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Informe ISU (PDF)
                    </a>
                </nav>
            </div>

            <!-- BARRA DE NAVEGACIÓN Y FILTROS DE PERÍODO (SOLO EN PANTALLA) -->

            <div class="print:hidden bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Período del Reporte:</span>
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full font-bold text-xs">
                        {{ $periodoTitulo }}
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('reportes.isu', ['periodo' => 'semana']) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition duration-200 {{ $periodo === 'semana' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semana Actual
                    </a>
                    <a href="{{ route('reportes.isu', ['periodo' => 'quincena']) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition duration-200 {{ $periodo === 'quincena' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Quincena (1 al 15)
                    </a>
                    <a href="{{ route('reportes.isu', ['periodo' => 'mensual']) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition duration-200 {{ $periodo === 'mensual' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Mensual (Mes Completo)
                    </a>
                </div>
            </div>

            <!-- DOCUMENTO DE REPORTE PDF (IMAGEN REPLICADA) -->
            <div id="documento-isu-pdf" class="bg-white p-6 sm:p-10 rounded-2xl shadow-md border border-gray-200 mx-auto max-w-[900px]">
                
                <!-- ENCABEZADO REPORTE ISU -->
                <div class="text-center mb-6">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-800 uppercase tracking-tight font-sans mb-3">
                        INFORME DE SATISFACCIÓN DEL USUARIO (ISU)
                    </h1>
                    <div class="inline-block bg-slate-100 text-slate-700 px-6 py-1.5 rounded-xl font-semibold text-xs sm:text-sm border border-slate-200">
                        <span class="font-bold">Periodo:</span> {{ $periodoTitulo }} &nbsp;|&nbsp; <span class="font-bold">Comedor:</span> Corporativo Central
                    </div>
                </div>

                <!-- SECCIÓN 1 Y SECCIÓN 2 GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 items-stretch">
                    
                    <!-- SECCIÓN 1: RESUMEN EJECUTIVO (ISU) -->
                    <div class="border border-slate-200 rounded-2xl p-5 flex flex-col justify-between bg-slate-50/50">
                        <div>
                            <!-- Header de Sección -->
                            <div class="flex items-center gap-3 mb-4">
                                <span class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-black text-sm shadow-sm">1</span>
                                <h2 class="font-extrabold text-slate-800 text-base uppercase tracking-tight">
                                    RESUMEN EJECUTIVO (ISU)
                                </h2>
                            </div>

                            <!-- Arch/Gauge Widget Visual -->
                            <div class="relative flex flex-col items-center justify-center my-4 py-2">
                                <div class="w-48 h-24 overflow-hidden relative">
                                    <!-- Semi-circle Track -->
                                    <div class="w-48 h-48 rounded-full border-[18px] border-slate-200 border-t-emerald-500 border-r-emerald-500 border-b-transparent border-l-cyan-500 absolute top-0 left-0 transform rotate-[-45deg]"></div>
                                    <!-- Gauge Value text -->
                                    <div class="absolute bottom-0 inset-x-0 text-center">
                                        <div class="text-xs font-bold text-slate-500 uppercase tracking-widest">CUMPLIMIENTO MÍNIMO CONTRACTUAL</div>
                                        <div class="text-3xl font-black text-slate-900 mt-1">{{ number_format($indiceGlobal, 1) }}%</div>
                                    </div>
                                </div>
                                <div class="text-[10px] text-slate-400 font-semibold uppercase mt-3">CUMPLIMIENTO MÍNIMO CONTRACTUAL</div>
                            </div>
                        </div>

                        <!-- Footer Pill de la Sección 1 -->
                        <div class="mt-4 bg-slate-100 border border-slate-200 rounded-xl p-3 text-center">
                            <span class="text-xs font-bold text-slate-700">
                                Índice Global: <span class="text-slate-900 font-black">{{ number_format($indiceGlobal, 1) }}%</span> - Estado: <span class="{{ $indiceGlobal >= 80 ? 'text-emerald-700' : 'text-amber-700' }} font-bold">{{ $indiceGlobal >= 80 ? 'Aprobado' : 'En Proceso' }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: DETALLE POR CRITERIOS -->
                    <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50 flex flex-col justify-between">
                        <div>
                            <!-- Header de Sección -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-black text-sm shadow-sm">2</span>
                                    <h2 class="font-extrabold text-slate-800 text-base uppercase tracking-tight">
                                        DETALLE POR CRITERIOS
                                    </h2>
                                </div>
                                <!-- Chef icon -->
                                <span class="text-slate-600">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </span>
                            </div>

                            <!-- Gráfica de Barras Horizontal Vectorial SVG -->
                            <div class="mt-2 w-full overflow-hidden">
                                @php
                                    $criteriosItems = [
                                        ['key' => 'calidad', 'label' => 'Calidad de Alimentos', 'icon' => '🍳', 'color' => '#1e293b'],
                                        ['key' => 'limpieza', 'label' => 'Limpieza e Higiene', 'icon' => '🧹', 'color' => '#1e293b'],
                                        ['key' => 'temperatura', 'label' => 'Temperatura Adecuada', 'icon' => '🌡️', 'color' => '#0d9488'],
                                        ['key' => 'atencion', 'label' => 'Atención y Eficiencia', 'icon' => '👨‍🍳', 'color' => '#1e293b'],
                                        ['key' => 'presentacion', 'label' => 'Presentación', 'icon' => '🍱', 'color' => '#1e293b'],
                                    ];
                                @endphp

                                <svg viewBox="0 0 420 185" class="w-full h-auto">
                                    @foreach($criteriosItems as $idx => $item)
                                        @php
                                            $val = max(0, min(100, $promediosCriterios[$item['key']] ?? 0));
                                            $y = 6 + ($idx * 35);
                                            $barTrackX = 135;
                                            $barMaxW = 245;
                                            $barW = max(35, ($val / 100) * $barMaxW);
                                            $valText = number_format($val, 1) . '%';
                                        @endphp
                                        <g>
                                            <!-- Etiqueta Criterio (Izquierda) -->
                                            <text x="0" y="{{ $y + 16 }}" font-size="11" font-weight="700" fill="#334155" font-family="sans-serif">{{ $item['label'] }}</text>
                                            
                                            <!-- Pista de Fondo de Barra -->
                                            <rect x="{{ $barTrackX }}" y="{{ $y }}" width="{{ $barMaxW }}" height="24" rx="4" fill="#e2e8f0" />
                                            
                                            <!-- Barra Horizontal Rellena -->
                                            <rect x="{{ $barTrackX }}" y="{{ $y }}" width="{{ $barW }}" height="24" rx="4" fill="{{ $item['color'] }}" />
                                            
                                            <!-- Valor % dentro de la Barra -->
                                            <text x="{{ $barTrackX + $barW - 8 }}" y="{{ $y + 16 }}" text-anchor="end" font-size="10" font-weight="900" fill="#ffffff" font-family="sans-serif">{{ $valText }}</text>
                                            
                                            <!-- Icono Criterio (Derecha) -->
                                            <text x="395" y="{{ $y + 17 }}" font-size="13" text-anchor="middle">{{ $item['icon'] }}</text>
                                        </g>
                                    @endforeach
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- SECCIÓN 4: ANÁLISIS DE TENDENCIA TRIMESTRAL (GRÁFICA DE ÁREA) -->
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-black text-sm shadow-sm">4</span>
                            <h2 class="font-extrabold text-slate-800 text-base uppercase tracking-tight">
                                ANÁLISIS DE TENDENCIA TRIMESTRAL
                            </h2>
                        </div>
                        <span class="text-slate-600">🍲</span>
                    </div>

                    <!-- Gráfica de Área Vectorial SVG (Promedio Conversión) -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200">
                        @php
                            $xCoords = [75, 225, 375, 525];
                            $pointsArray = [];
                            $polygonPoints = ["75,130"];
                            $polylinePoints = [];

                            foreach ($tendenciaTrimestral as $idx => $item) {
                                $val = max(0, min(100, $item['promedio_conversion']));
                                $x = $xCoords[$idx] ?? (75 + $idx * 150);
                                $y = 130 - (($val / 100) * 105);
                                $pointsArray[] = ['x' => $x, 'y' => $y, 'val' => $item['promedio_conversion'], 'label' => $item['mes'] . ' ' . $item['year']];
                                $polygonPoints[] = "{$x},{$y}";
                                $polylinePoints[] = "{$x},{$y}";
                            }
                            $polygonPoints[] = "525,130";

                            $polygonStr = implode(' ', $polygonPoints);
                            $polylineStr = implode(' ', $polylinePoints);
                        @endphp

                        <div class="w-full overflow-hidden">
                            <svg viewBox="0 0 600 165" class="w-full h-auto max-h-52">
                                <defs>
                                    <linearGradient id="isuAreaGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.45"/>
                                        <stop offset="100%" stop-color="#93c5fd" stop-opacity="0.08"/>
                                    </linearGradient>
                                </defs>

                                <!-- Líneas de guía de porcentaje -->
                                <line x1="45" y1="25" x2="550" y2="25" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3 3"/>
                                <text x="40" y="28" text-anchor="end" font-size="9" font-weight="bold" fill="#94a3b8">100%</text>

                                <line x1="45" y1="46" x2="550" y2="46" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3 3"/>
                                <text x="40" y="49" text-anchor="end" font-size="9" font-weight="bold" fill="#94a3b8">80%</text>

                                <line x1="45" y1="67" x2="550" y2="67" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3 3"/>
                                <text x="40" y="70" text-anchor="end" font-size="9" font-weight="bold" fill="#94a3b8">60%</text>

                                <line x1="45" y1="130" x2="550" y2="130" stroke="#cbd5e1" stroke-width="1"/>
                                <text x="40" y="133" text-anchor="end" font-size="9" font-weight="bold" fill="#94a3b8">0%</text>

                                <!-- Relleno de Área (Gráfica de Área) -->
                                <polygon points="{{ $polygonStr }}" fill="url(#isuAreaGradient)"/>

                                <!-- Línea de Tendencia -->
                                <polyline points="{{ $polylineStr }}" fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>

                                <!-- Puntos de Datos y Etiquetas -->
                                @foreach($pointsArray as $pt)
                                    <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="#1d4ed8" stroke="#ffffff" stroke-width="2.5"/>
                                    <text x="{{ $pt['x'] }}" y="{{ $pt['y'] - 8 }}" text-anchor="middle" font-size="11" font-weight="bold" fill="#1e293b">{{ $pt['val'] }}%</text>
                                    <text x="{{ $pt['x'] }}" y="148" text-anchor="middle" font-size="10" font-weight="bold" fill="#64748b">{{ $pt['label'] }}</text>
                                @endforeach
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 3: HALLAZGOS CRÍTICOS & PLAN DE ACCIÓN (UBICADO AL FINAL DEL DOCUMENTO COMO NOTAS) -->
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-black text-sm shadow-sm">3</span>
                            <h2 class="font-extrabold text-slate-800 text-base uppercase tracking-tight">
                                HALLAZGOS CRÍTICOS & PLAN DE ACCIÓN
                            </h2>
                        </div>
                        <span class="text-slate-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </span>
                    </div>

                    <!-- Tabla de Hallazgos y Plan de Acción -->
                    <div class="overflow-hidden border border-slate-200 rounded-xl bg-white">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                            <thead class="bg-slate-800 text-white uppercase font-black tracking-wider">
                                <tr>
                                    <th scope="col" class="py-3 px-4 w-1/2 text-center">HALLAZGOS</th>
                                    <th scope="col" class="py-3 px-4 w-1/2 text-center">PLAN DE ACCIÓN</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 font-medium text-slate-700">
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3 px-4 align-top">
                                        1. Temperatura inconsistente en platillos principales (horas pico de servicio).
                                    </td>
                                    <td class="py-3 px-4 align-top">
                                        1. Ajuste y calibración de equipos de calentamiento continuo. (Responsable: Proveedor, Fecha: 15 Nov)
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3 px-4 align-top">
                                        2. Solicitud de mayor variedad en barras de ensaladas y guisados.
                                    </td>
                                    <td class="py-3 px-4 align-top">
                                        2. Introducción de nuevo menú con opciones rotativas semanales. (Responsable: Proveedor, Fecha: 01 Dic)
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
