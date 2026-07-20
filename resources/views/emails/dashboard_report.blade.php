<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Estadísticas de Comedor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
        }
        .header {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .content {
            padding: 24px;
        }
        .kpi-grid {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }
        .kpi-card {
            display: table-cell;
            width: 25%;
            padding: 12px;
            background: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 8px;
            text-align: center;
        }
        .kpi-title {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
        }
        .kpi-value {
            font-size: 22px;
            font-weight: bold;
            color: #111827;
            margin-top: 4px;
        }
        .table-section {
            margin-top: 20px;
        }
        .table-section h3 {
            font-size: 15px;
            color: #374151;
            margin-bottom: 10px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th, td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #f9fafb;
            color: #4b5563;
        }
        .footer {
            background: #f9fafb;
            padding: 16px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍽️ Reporte Resumen de Comedor</h1>
            <p>Frecuencia: {{ $periodo }} | Generado el {{ now()->format('d/m/Y H:i') }} hrs</p>
        </div>

        <div class="content">
            @if(!empty($notas))
                <div style="background-color: #eef2ff; border-left: 4px solid #4f46e5; padding: 12px; margin-bottom: 20px; font-size: 13px; color: #3730a3;">
                    <strong>Nota adicional:</strong> {{ $notas }}
                </div>
            @endif

            <!-- KPI Summary Table -->
            <table style="margin-bottom: 24px;">
                <thead>
                    <tr>
                        <th>Métrica</th>
                        <th style="text-align: right;">Resultado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Total de Empleados en Sistema</strong></td>
                        <td style="text-align: right; color: #111827; font-weight: bold;">{{ $stats['totalEmpleados'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Empleados Activos / Inactivos</td>
                        <td style="text-align: right;">{{ $stats['empleadosActivos'] ?? 0 }} Activos | {{ $stats['empleadosInactivos'] ?? 0 }} Inactivos</td>
                    </tr>
                    <tr>
                        <td><strong>Comidas Servidas Hoy</strong></td>
                        <td style="text-align: right; color: #16a34a; font-weight: bold;">{{ $stats['accesosHoy'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td><strong>Comidas Servidas Este Mes</strong></td>
                        <td style="text-align: right; color: #4f46e5; font-weight: bold;">{{ $stats['accesosMes'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Promedio Diario Estimado</td>
                        <td style="text-align: right;">{{ $stats['promedioDiario'] ?? 0 }} comidas/día</td>
                    </tr>
                </tbody>
            </table>

            <!-- Department breakdown summary -->
            @if(!empty($stats['deptLabels']) && !empty($stats['deptValues']))
                <div class="table-section">
                    <h3>📊 Consumo por Departamento</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Departamento</th>
                                <th style="text-align: right;">Total Comidas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['deptLabels'] as $index => $dept)
                                <tr>
                                    <td>{{ $dept }}</td>
                                    <td style="text-align: right; font-weight: bold;">{{ $stats['deptValues'][$index] ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="footer">
            Este es un correo automático generado por el Sistema de Control de Comedor.<br>
            © {{ date('Y') }} Soluciones Integrales RyJ. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
