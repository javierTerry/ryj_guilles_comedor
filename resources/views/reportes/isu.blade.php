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
                    
                    <!-- SECCIÓN 1: RESUMEN EJECUTIVO (ISU) - GAUGE CHART DIVIDIDO POR ESTADO CONTRACTUAL -->
                    @php
                        $val = max(0, min(100, (float)($indiceGlobal ?? 0)));
                        
                        // Rotación de la aguja velocímetro:
                        // 0% => -90° (horizontal izquierda)
                        // 50% => 0° (vertical hacia arriba)
                        // 100% => +90° (horizontal derecha)
                        $rotDeg = round(-90 + ($val / 100 * 180), 2);
                        
                        // Posición del cursor en el arco (radio r = 86, centro = 160, 130)
                        $knobAngleDeg = 180 - ($val / 100 * 180);
                        $knobAngleRad = deg2rad($knobAngleDeg);
                        $knobX = round(160 + 86 * cos($knobAngleRad), 2);
                        $knobY = round(130 - 86 * sin($knobAngleRad), 2);

                        // Reglas de estado contractual dinámico solicitadas:
                        // 1. Menor a 80%: Rojo (Incumplimiento Crítico)
                        // 2. Entre 80% y 85%: Amarillo (Cumplimiento Mínimo)
                        // 3. Encima de 85% hasta 100%: Verde (Cumplimiento Óptimo, degradado de tenue a fuerte)
                        if ($val < 80) {
                            $gaugeSolidColor = '#ef4444';
                            $estadoNombre = 'Incumplimiento Crítico';
                            $estadoDesc = 'Por debajo del umbral contractual (< 80%)';
                            $badgeBg = 'bg-rose-50';
                            $badgeText = 'text-rose-700';
                            $badgeBorder = 'border-rose-200';
                            $badgeIcon = '🔴';
                        } elseif ($val <= 85) {
                            $gaugeSolidColor = '#f59e0b';
                            $estadoNombre = 'Cumplimiento Mínimo';
                            $estadoDesc = 'Rango contractual aceptable (80% - 85%)';
                            $badgeBg = 'bg-amber-50';
                            $badgeText = 'text-amber-800';
                            $badgeBorder = 'border-amber-200';
                            $badgeIcon = '🟡';
                        } else {
                            // De verde tenue rgb(167, 243, 208) a verde fuerte rgb(21, 128, 61)
                            $factor = ($val - 85) / 15; // 0.0 en 85%, 1.0 en 100%
                            $rDyn = round(167 - ($factor * (167 - 21)));
                            $gDyn = round(243 - ($factor * (243 - 128)));
                            $bDyn = round(208 - ($factor * (208 - 61)));
                            $gaugeSolidColor = "rgb({$rDyn}, {$gDyn}, {$bDyn})";
                            
                            $estadoNombre = $val >= 95 ? 'Cumplimiento Sobresaliente' : 'Cumplimiento Óptimo';
                            $estadoDesc = 'Supera el umbral contractual (> 85%)';
                            $badgeBg = 'bg-emerald-50';
                            $badgeText = 'text-emerald-800';
                            $badgeBorder = 'border-emerald-200';
                            $badgeIcon = '🟢';
                        }
                    @endphp

                    <div class="border border-slate-200 rounded-2xl p-5 flex flex-col justify-between bg-slate-50/50">
                        <div>
                            <!-- Header de Sección con Leyenda Visual de los 3 Colores -->
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-black text-sm shadow-sm">1</span>
                                    <h2 class="font-extrabold text-slate-800 text-base uppercase tracking-tight">
                                        RESUMEN EJECUTIVO (ISU)
                                    </h2>
                                </div>
                                <!-- Micro-Leyenda de división de colores -->
                                <div class="flex items-center gap-1 text-[9.5px] font-bold">
                                    <span class="px-1.5 py-0.5 rounded bg-red-100 text-red-700">🔴 &lt;80%</span>
                                    <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">🟡 80-85%</span>
                                    <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">🟢 &gt;85%</span>
                                </div>
                            </div>

                            <!-- GAUGE CHART CON DIVISIONES DE COLOR VISIBLES (PDF Y PANTALLA) -->
                            <div class="relative flex flex-col items-center justify-center pt-2 pb-1">
                                <svg viewBox="0 0 320 160" class="w-full max-w-[295px] h-auto overflow-visible select-none">
                                    <defs>
                                        <!-- Gradiente para el Sector 3 (> 85% a 100%): Verde tenue a Verde fuerte -->
                                        <linearGradient id="gaugeSectorGreenGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#86efac" />  <!-- Verde tenue en 85% -->
                                            <stop offset="50%" stop-color="#22c55e" /> <!-- Verde medio -->
                                            <stop offset="100%" stop-color="#14532d" /><!-- Verde fuerte en 100% -->
                                        </linearGradient>
                                    </defs>

                                    <!-- 1. SECTORES VISIBLEMENTE DIVIDIDOS (ROJO, AMARILLO Y VERDE) -->
                                    <!-- SECTOR 1: 🔴 ROJO (< 80%, Incumplimiento Crítico) -->
                                    <path d="M 74 130 A 86 86 0 0 1 228.23 77.65" 
                                          fill="none" 
                                          stroke="#ef4444" 
                                          stroke-width="17" 
                                          stroke-linecap="round" />
                                    
                                    <!-- SECTOR 2: 🟡 AMARILLO (80% - 85%, Cumplimiento Mínimo) -->
                                    <path d="M 230.87 81.29 A 86 86 0 0 1 235.58 88.96" 
                                          fill="none" 
                                          stroke="#f59e0b" 
                                          stroke-width="17" 
                                          stroke-linecap="butt" />
                                    
                                    <!-- SECTOR 3: 🟢 VERDE (> 85% - 100%, Cumplimiento Óptimo: tenue a fuerte) -->
                                    <path d="M 237.62 92.98 A 86 86 0 0 1 246 130" 
                                          fill="none" 
                                          stroke="url(#gaugeSectorGreenGradient)" 
                                          stroke-width="17" 
                                          stroke-linecap="round" />

                                    <!-- 2. MARCADORES DE UMBRAL Y ETIQUETAS DE ESCALA -->
                                    <!-- Tick 80% (Límite Rojo / Amarillo) -->
                                    <line x1="228.23" y1="77.65" x2="237.5" y2="71.8" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" />
                                    <text x="245" y="68" font-size="9" font-weight="900" fill="#d97706" text-anchor="start" font-family="sans-serif">80%</text>

                                    <!-- Tick 85% (Límite Amarillo / Verde) -->
                                    <line x1="237.62" y1="92.98" x2="247.3" y2="88.0" stroke="#10b981" stroke-width="2" stroke-linecap="round" />
                                    <text x="254" y="87" font-size="9" font-weight="900" fill="#059669" text-anchor="start" font-family="sans-serif">85%</text>

                                    <!-- Etiquetas de Base 0% y 100% -->
                                    <text x="54" y="146" font-size="10" font-weight="700" fill="#94a3b8" text-anchor="middle" font-family="sans-serif">0%</text>
                                    <text x="266" y="146" font-size="10" font-weight="700" fill="#94a3b8" text-anchor="middle" font-family="sans-serif">100%</text>

                                    <!-- 3. AGUJA DE PRECISIÓN DEL TACÓMETRO (APUNTA AL VALOR ACTUAL) -->
                                    <g transform="rotate({{ $rotDeg }}, 160, 130)">
                                        <polygon points="157.5,130 162.5,130 160.8,58 159.2,58" fill="#1e293b" opacity="0.95" />
                                        <circle cx="160" cy="130" r="7.5" fill="#0f172a" />
                                        <circle cx="160" cy="130" r="3" fill="#ffffff" />
                                    </g>

                                    <!-- 4. CURSOR / PUNTERO INDICADOR EN EL ARCO -->
                                    <circle cx="{{ $knobX }}" cy="{{ $knobY }}" r="8" fill="#ffffff" stroke="#0f172a" stroke-width="3" />
                                    <circle cx="{{ $knobX }}" cy="{{ $knobY }}" r="3.5" fill="{{ $gaugeSolidColor }}" />

                                    <!-- 5. VALOR NUMÉRICO CENTRAL Y ETIQUETA -->
                                    <text x="160" y="98" text-anchor="middle" font-size="32" font-weight="900" fill="#0f172a" font-family="sans-serif">
                                        {{ number_format($val, 1) }}%
                                    </text>
                                    <text x="160" y="115" text-anchor="middle" font-size="9" font-weight="800" fill="#64748b" letter-spacing="1.2" font-family="sans-serif">
                                        ÍNDICE ISU OBTENIDO
                                    </text>
                                </svg>
                            </div>
                        </div>

                        <!-- ESTADO CONTRACTUAL DINÁMICO -->
                        <div class="mt-4">
                            <!-- Badge de Estado Actual Dinámico -->
                            <div class="flex items-center justify-between px-4 py-2.5 rounded-xl {{ $badgeBg }} border {{ $badgeBorder }} shadow-2xs">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-base leading-none">{{ $badgeIcon }}</span>
                                    <div>
                                        <div class="text-xs font-black {{ $badgeText }} uppercase tracking-tight leading-tight">
                                            {{ $estadoNombre }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-medium leading-tight">
                                            {{ $estadoDesc }}
                                        </div>
                                    </div>
                                </div>
                                <span class="text-sm font-black {{ $badgeText }}">
                                    {{ number_format($val, 1) }}%
                                </span>
                            </div>
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

                <!-- SECCIÓN 3: ANÁLISIS DE TENDENCIA TRIMESTRAL (GRÁFICA DE ÁREA) -->
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-black text-sm shadow-sm">3</span>
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

                <!-- SECCIÓN 4: RETROALIMENTACIÓN DE USUARIOS (5 COMENTARIOS ALEATORIOS) -->
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-black text-sm shadow-sm">4</span>
                            <div>
                                <h2 class="font-extrabold text-slate-800 text-base uppercase tracking-tight">
                                    RETROALIMENTACIÓN DE USUARIOS
                                </h2>
                                <p class="text-[11px] text-slate-500 font-medium">Muestra aleatoria de opiniones de comensales (> 10 caracteres)</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-slate-600 shadow-2xs flex items-center gap-1.5">
                            <span>💬</span> 5 Comentarios Aleatorios
                        </span>
                    </div>

                    <!-- Listado de Comentarios -->
                    <div class="space-y-2.5">
                        @forelse($comentariosAleatorios as $idx => $coment)
                            <div class="bg-white border border-slate-200/90 rounded-xl p-3.5 shadow-2xs flex items-start gap-3">
                                <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-700 font-black text-xs flex items-center justify-center shrink-0 mt-0.5 border border-indigo-100">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-700 leading-relaxed italic">
                                        "{{ $coment->comentarios }}"
                                    </p>
                                    @if(!empty($coment->fecha))
                                        <div class="flex items-center gap-3 mt-1.5 text-[10.5px] text-slate-400 font-medium">
                                            <span>📅 {{ \Carbon\Carbon::parse($coment->fecha)->format('d/m/Y') }}</span>
                                            @if(!empty($coment->calificacion))
                                                <span class="text-amber-500 font-bold">
                                                    {{ str_repeat('★', (int)$coment->calificacion) }} ({{ $coment->calificacion }}/5)
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="bg-white border border-dashed border-slate-300 rounded-xl p-6 text-center text-xs text-slate-400">
                                No se encontraron comentarios registrados con más de 10 caracteres.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
