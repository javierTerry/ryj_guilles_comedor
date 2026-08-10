# Sistema de Control de Comedor y Empleados

Este es un sistema basado en Laravel diseñado para gestionar el registro diario de consumo de alimentos de los empleados, permitiendo un flujo de escaneo rápido y estadísticas avanzadas para administración.

---

## 🚀 Características Principales

### 1. Registro de Comedor (Acceso Público Kiosco)
* **URL Pública:** `/comedor` (Accesible para cualquiera en red local, no requiere inicio de sesión).
* **Flujo de Escaneo Rápido:** Permite registrar accesos digitando el número de empleado o mediante lectores de códigos de barras.
* **Control de Reservación Obligatoria:** Verifica que el empleado cuente con una reservación registrada para la fecha actual antes de permitir el consumo (excepto en el horario de acceso libre o si el colaborador se encuentra en la lista de excepciones).
* **Validación de Horarios Reservados y Capacidades:**
  * **12:30 p.m. a 1:00 p.m.:** Capacidad de 120 lugares (Ventana de ingreso: 12:00 a 13:15).
  * **1:15 p.m. a 1:45 p.m.:** Capacidad de 140 lugares (Ventana de ingreso: 13:00 a 14:00).
  * **2:00 p.m. a 2:30 p.m.:** Capacidad de 140 lugares (Ventana de ingreso: 13:45 a 14:45).
  * **2:45 p.m. a 3:15 p.m.:** Capacidad de 140 lugares (Ventana de ingreso: 14:30 a 15:30).
  * **3:30 p.m. a 4:30 p.m.:** Acceso libre sin restricción de cupo (Ventana de ingreso: 15:15 a 16:30).
* **Control de Duplicados:** Restricción a nivel de base de datos (`UNIQUE [empleado_id, fecha]`) para impedir que un empleado registre más de una comida por día.
* **Estructura Lado a Lado Responsiva en Proporción 3:5:2 (`30% - 50% - 20%`):**
  1. **Scanner Control Box (`#scanner-card`):** Formulario de escaneo (`lg:flex-[3] min-w-0` - 30% del espacio relativo).
  2. **Status Feedback Panels (`#status-card`):** Panel de estatus expandido (`lg:flex-[5] min-w-0` - 50% del espacio relativo).
  3. **Tarjeta Dedicada de Contador de Accesos (`#counter-card`):** Tarjeta compacta (`lg:flex-[2] min-w-0` - 20% del espacio relativo) con número responsivo (`text-3xl sm:text-4xl md:text-5xl font-black font-mono`).
  * **Sin desbordamientos:** Los factores de crecimiento de Flexbox deducen automáticamente los espacios `gap-6` del ancho total de pantalla, y `min-w-0` garantiza que la interfaz sea 100% responsiva en cualquier monitor o pantalla de kiosco sin generar scroll horizontal.
* **Burbujas Contador Responsivas de Accesos (`Nº Entrada`):**
  * Cada registro de entrada en el menú comedor cuenta con una **burbuja contador en la tabla de historial** (`#1`, `#42`, `#9999`) y en la tarjeta de **Acceso Autorizado**.
  * Diseñadas con tipografía responsiva de amplio tamaño (`text-base sm:text-lg md:text-xl font-black`) y un ancho mínimo dinámico (`min-w-[4.5rem]`) para garantizar la óptima visibilidad y encuadre de números de hasta **4 dígitos (9999)** sin rompimiento de diseño ni saltos de línea.
* **Canal Dedicado de Logs (`comedor`):** Registro de trazabilidad exclusivo en `storage/logs/comedor.log` que captura intentos de escaneo, accesos autorizados, número consecutivo del día, rechazos y horario de reservación.
* **Historial de Registros:** El listado de accesos del día está protegido; los usuarios autenticados pueden consultar el consecutivo en tiempo real.

### 2. Panel de Estadísticas (Dashboard Administrativo)
* **URL Protegida:** `/dashboard` (Solo para administradores autenticados).
* **Exportación a PDF con Gráficas en Tiempo Real:** Botón en el encabezado para descargar instantáneamente el dashboard completo en formato PDF, capturando las gráficas interactivas de Chart.js y tarjetas KPI.
* **Envío Recurrente por Correo:** Modal interactivo para enviar el resumen del dashboard por email o programar su despacho periódico (diario, semanal, mensual).
* **Métricas KPI:**
  * Total de empleados en el sistema.
  * Cantidad de empleados activos vs. inactivos.
  * Total de comidas servidas hoy.
  * Consumo mensual acumulado y promedio diario estimado.
* **Gráficos Interactivos (Chart.js):**
  * **Tendencia Diaria (Últimos 15 días):** Línea interactiva con curvas suaves que muestra las fluctuaciones de consumo.
  * **Distribución de Horas Pico:** Gráfico de barras que agrupa las comidas por hora (06:00 a 20:00) para identificar las horas más concurridas.
  * **Consumo Mensual:** Gráfico acumulativo por mes para el año en curso.
  * **Uso por Departamento:** Gráfico de dona (Doughnut) que muestra qué áreas o departamentos de la empresa consumen más alimentos.

### 3. Gestión de Empleados
* **Rutas Protegidas:** `/empleados` (CRUD completo de personal).
* **Cálculo de Visitas de la Semana Actual (Lunes a Viernes):** La vista lista siempre a la totalidad de los colaboradores, pero restringe la métrica de visitas en la tabla al consumo acumulado de Lunes a Viernes de la semana en curso.
* **Importación Masiva y Campo Correo:** Subida y procesamiento de listas de empleados mediante archivos CSV con inclusión obligatoria del correo electrónico y descarga de plantilla oficial actualizada.
* **Estado de Empleado:** Switch interactivo para activar o desactivar empleados de forma rápida (un empleado desactivado no podrá registrar comidas).

### 4. Módulo de Reportes y Submenús (General, Visitas y Reservaciones)
* **URL Protegida:** `/reportes` (Acceso exclusivo para usuarios autenticados mediante menú desplegable y móvil "Reportes").
* **Estructura de Submenús:**
  * **Reporte General (`/reportes`):** Muestra el resumen del catálogo de colaboradores acotando el conteo acumulado de sus visitas al comedor por rango de fecha.
  * **Reporte de Visitas (`/reportes/visitas`):** Consulta detallada de cada acceso individual al comedor con su horario exacto de ingreso (`fecha_hora`).
  * **Reporte de Reservas (`/reportes/reservas`):** Consulta detallada de reservaciones agendadas por día y horario por los colaboradores.
* **Valores e Información en el Reporte de Reservaciones:**
  * Número de empleado.
  * Nombre del colaborador.
  * Correo electrónico.
  * Departamento y Puesto.
  * Fecha de reservación.
  * Horario reservado (12:30 p.m., 13:15 p.m., 14:00 p.m., 14:45 p.m., 15:30 p.m.).
  * Estatus del empleado (Activo / Inactivo).
* **Comportamiento del Filtro por Defecto:**
  * **Reporte de Visitas:** Toma automáticamente la **semana actual** (Lunes a Domingo en curso).
  * **Reporte de Reservas:** Toma por defecto únicamente las reservaciones del **día actual** (`Carbon::today()`).
* **Filtros Avanzados y Rango de Fechas:** Permite consultar acotando la información por:
  * **Rango de Fechas (`fecha_inicio` y `fecha_fin`)**: Permite extender o personalizar el rango de reservaciones o visitas.
  * **Nombre o Número de Empleado** (búsqueda parcial).
  * **Departamento** (lista desplegable dinámica).
  * **Estatus del Empleado** (Todos / Activos / Inactivos).
  * **Horario Reservado** (12:30, 13:15, 14:00, 14:45, 15:30).
  * **Registros por Página** (25, 50, 75 o 100 por página).
* **Descarga Masiva en CSV:** Generación en tiempo real de archivos CSV codificados en UTF-8 con BOM tanto para el Reporte General (`/reportes/exportar`), Reporte de Visitas (`/reportes/visitas/exportar`) y Reporte de Reservaciones (`/reportes/reservas/exportar`).
* **Canales Dedicados de Logs:**
  * Canal **`reportes`**: Trazabilidad en `storage/logs/reportes.log` para el reporte general.
  * Canal **`visitas`**: Trazabilidad en `storage/logs/visitas.log` para el reporte de visitas.
  * Canal **`reservas`**: Trazabilidad en `storage/logs/reservas.log` para el reporte de reservaciones por día.


### 5. Encuesta de Satisfacción del Comedor (Acceso Público)
* **URL Pública:** `/encuesta` (Accesible sin necesidad de inicio de sesión).
* **Validación de Comensal por AJAX & SweetAlert2:**
  * Para habilitar el formulario de evaluación, el comensal ingresa su **número de empleado**.
  * **Verificación de Ingreso al Comedor:** El sistema valida contra la base de datos que el colaborador **haya registrado su acceso al comedor en la fecha actual** (`registro_comedors`). Si no ha ingresado hoy, se muestra una alerta SweetAlert indicando: *"La encuesta es exclusivamente para usuarios que ya realizaron su ingreso al comedor el día de hoy."*
  * **Control de Unicidad Diaria (1 Encuesta por día):** Se restringe la encuesta a una sola vez al día por comensal (`UNIQUE [empleado_id, fecha]`). Si ya la realizó, se notifica mediante SweetAlert.
* **Criterios de Evaluación y Estrellas (1 a 5):**
  * **Calidad de alimentos:** Sabor, frescura y variedad *(Ponderación interna: 30%)*.
  * **Limpieza e higiene:** Instalaciones, utensilios y manipulación *(Ponderación interna: 25%)*.
  * **Temperatura adecuada:** Alimentos calientes (≥60°C) / fríos (≤7°C) *(Ponderación interna: 20%)*.
  * **Atención y eficiencia:** Tiempo de espera y trato personal *(Ponderación interna: 15%)*.
  * **Presentación:** Línea fría y caliente *(Ponderación interna: 10%)*.
  * *Nota:* Las ponderaciones internas son transparentes para el usuario comensal y se emplean exclusivamente para cálculos estadísticos internos.
* **Sistema de Calificación:**
  * **5 Estrellas:** Excelente (100%)
  * **4 Estrellas:** Bueno (80%)
  * **3 Estrellas:** Regular (60%)
  * **2 Estrellas:** Deficiente (40%)
  * **1 Estrella:** Muy deficiente (20%)
* **Cálculos y Conversiones de Base de Datos:**
  * `calificacion`: Promedio decimal (1.00 a 5.00) de las 5 evaluaciones.
  * `conversion`: Campo compuesto en porcentaje: `((calificacion / 5) * 100)`.
  * `ponderacion_total`: Promedio ponderado de negocio: `(Calidad*0.30 + Limpieza*0.25 + Temperatura*0.20 + Atención*0.15 + Presentación*0.10)`.
### 7. Informe de Satisfacción del Usuario (ISU) en PDF
* **URL Protegida:** `/reportes/isu` (Acceso desde el submenú de Reportes).
* **Generación de Reporte PDF Filtrable por Período:**
  * **Semana Actual:** Filtra automáticamente de Lunes a Domingo de la semana en curso (calculado dinámicamente sin importar qué día de la semana se pida).
  * **Quincena:** Filtra el período del 1 al 15 del mes actual.
  * **Mensual:** Filtra desde el 1er día hasta el último día del mes actual.
* **4 Secciones Principales (Diseño Réplica Oficial):**
  * **Sección 1: Resumen Ejecutivo ISU:** Indicador visual tipo medidor/arco de cumplimiento mínimo contractual e índice global.
  * **Sección 2: Detalle por Criterios:** Gráfica de Barras con la evaluación promedio (%) de Calidad de Alimentos, Limpieza e Higiene, Temperatura Adecuada, Atención y Eficiencia, y Presentación.
  * **Sección 3: Hallazgos Críticos & Plan de Acción:** Tabla estructurada de notas al pie con hallazgos y acciones acordadas.
  * **Sección 4: Análisis de Tendencia Trimestral:** Gráfica de Área vectorial SVG con tendencia comparativa de los últimos 4 meses evaluando el Promedio de Conversión.
* **Botón de Imprimir / Descargar PDF:** Optimizado con reglas CSS `@media print` para renderizado perfecto en A4.
* **Canal Dedicado de Logs (`isu_report`):** Almacena la trazabilidad de consultas del informe ISU en `storage/logs/isu_report.log`.

---
* **URL Pública:** `/reservar` (Accesible para cualquiera en red local).
* **Diseño e Interacción:** Inspirado en "Comedor GILOU" con fuentes personalizadas, inputs minimalistas con iconos de Heroicons y selección rápida de horarios (12:30 p.m., 13:45 p.m., 14:45 p.m., 15:45 p.m.) reactiva mediante Alpine.js.
* **Validaciones del Sistema:**
  * Requisito de **Número de Colaborador y Correo Electrónico registrado** obligatorio para validar la identidad antes de agendar.
  * El número de colaborador debe ser numérico y no exceder los 10 dígitos.
  * **Control de Apertura Diaria (8:00 a.m.):** Las reservaciones solo pueden realizarse a partir de las 8:00 a.m. Muestra aviso informativo si se intenta acceder previamente.
  * **Corte Anticipado de 15 Minutos por Horario:** Cierre automático del horario 15 minutos antes de su inicio (12:30 cierra a las 12:15, 13:45 a las 13:30, 14:45 a las 14:30, 15:45 a las 15:30).
  * El colaborador debe existir previamente en el sistema y estar activo.
  * Límite estricto de una reservación por día por empleado y cupo máximo de 180 lugares por horario.
  * **Flujo de Confirmación AJAX:** Antes de enviar el formulario, el sistema valida la identidad del colaborador mediante AJAX y presenta una tarjeta con el nombre, fecha y horario para confirmar o cancelar.
  * **Pre-chequeo de Duplicados en Tiempo Real:** El sistema comprueba si el colaborador ya reservó hoy y muestra una advertencia indicándole el horario exacto que tiene registrado, bloqueando el avance al modal de confirmación.
  * **Soporte para Despliegues en Subcarpetas:** Detección dinámica de rutas en JS mediante Laravel helper `route()` para evitar fallos de redirección 404 en URL relativas.
  * Feedback interactivo de éxito o error al instante implementando SweetAlert2.

---

## 🛠️ Stack Tecnológico

* **Framework:** Laravel (PHP 8.x)
* **Frontend:** TailwindCSS, Blade Templates, Alpine.js (para la reactividad de escaneo y alertas).
* **Base de Datos:** MySQL / MariaDB (con control estricto de zona horaria).
* **Librerías Visuales:** Chart.js (gráficos interactivos) y SweetAlert2 (alertas flotantes de estado).

---

## ⚙️ Configuración Importante de Zona Horaria

Para garantizar que los registros y las estadísticas de consumo diario coincidan con la hora local de México y evitar un desfase de 6 horas respecto a UTC:

1. **Configuración de Laravel (`config/app.php`):**
   ```php
   'timezone' => env('APP_TIMEZONE', 'America/Mexico_City'),
   ```
2. **Conexión de Base de Datos (`config/database.php`):**
   Se sincronizó la sesión de conexión MySQL para utilizar el offset fijo de la Ciudad de México:
   ```php
   'mysql' => [
       'driver' => 'mysql',
       // ...
       'timezone' => env('DB_TIMEZONE', '-06:00'),
   ]
   ```
3. **Archivo de Entorno (`.env`):**
   ```env
   APP_TIMEZONE=America/Mexico_City
   DB_TIMEZONE=-06:00
   ```

---

## 📂 Estructura del Código Clave

* **Controladores:**
  * `DashboardController.php`: Lógica de consultas de agregación y preparación de datos para los gráficos.
  * `RegistroComedorController.php`: Lógica del registro de consumo e indexación pública del lector.
  * `EmpleadoController.php`: Lógica del CRUD e importación masiva de personal.
  * `ReservacionController.php`: Lógica del sistema público de reservaciones de comedor.
  * `ReporteController.php`: Lógica del módulo de reportes de visitas acotadas por rango de fecha y exportación CSV.
* **Modelos:**
  * `Empleado.php`: Modelo principal para la gestión de colaboradores.
  * `RegistroComedor.php`: Modelo para los consumos diarios del comedor.
  * `Reservacion.php`: Modelo para las citas y reservaciones de comedor GILOU.
  * `EmpleadoLog.php`: Bitácora de cambios para la auditoría de personal.
* **Vistas:**
  * `resources/views/dashboard.blade.php`: Contenedores y scripts de Chart.js para los 4 gráficos.
  * `resources/views/comedor/index.blade.php`: Pantalla del kiosco de escaneo.
  * `resources/views/reportes/index.blade.php`: Módulo de reportes de empleados con filtros de rango de fechas, resumen de visitas y exportación a CSV.
  * `resources/views/reportes/reservas.blade.php`: Módulo de reporte de reservaciones por día con filtros, tarjetas KPI y exportación CSV.
  * `resources/views/reservaciones/create.blade.php`: Formulario de reservación pública Comedor GILOU.
  * `resources/views/layouts/navigation.blade.php`: Menú de navegación dinámico adaptativo para visitantes y administradores.

## 📌 Historial de Versiones

* **v2.8.0 (Actual)**:
  * **Excepción de Acceso para Colaboradores**:
    * Implementación de una lista de excepciones inicial (hardcoded) para 4 números de empleado que les permite el ingreso al comedor sin necesidad de contar con reservación o sin importar el horario.
  * **Actualización de Capacidades de Horarios de Comedor**:
    * **1:15 p.m.** (13:15) y **2:00 p.m.** (14:00): Incrementado de 120 a **140 lugares**.
    * **2:45 p.m.** (14:45): Incrementado a **140 lugares**.
    * **12:30 p.m.** (12:30): Mantiene su capacidad de **120 lugares**.
  * **Leyendas Dinámicas en la Selección Rápida de Horario (UI)**:
    * Se removió la leyenda de texto "Cerrado" cuando un horario no tiene lugares disponibles o está inhabilitado.
    * En su lugar, la interfaz muestra dinámicamente la proporción actual de lugares reservados respecto a la capacidad total (ejemplo: `140/140`, `120/120`, `100/100`), manteniendo informados a los colaboradores sin mostrar la etiqueta "Cerrado".
  * **Canal Dedicado de Trazabilidad y Logging (`reservas_horarios`)**:
    * Configurado el nuevo canal de log `'reservas_horarios'` en `config/logging.php` con destino en `storage/logs/reservas_horarios.log`.
    * Registro completo de auditoría y trazabilidad para consultas de disponibilidad de horarios, intentos de reservación, validaciones de cupo, rechazos por duplicados/inactividad y confirmaciones AJAX.
* **v2.7.0**:
  * Creado el **Reporte de Reservaciones por Día** (`/reportes/reservas`), accesible exclusivamente para usuarios autenticados.
  * Configurada la carga por defecto al **día actual** (`Carbon::today()`) cuando no se especifican filtros de fecha.
  * Tarjetas KPI de resumen: *Total Reservaciones Registradas*, *Rango de Consulta* y *Paginación / Formato*.
  * Filtros dinámicos horizontales por Búsqueda de Colaborador (Nombre/Nº), Departamento, Estatus Empleado, Horario Reservado (12:30, 13:15, 14:00, 14:45, 15:30), Rango de Fechas y Paginación (25, 50, 75, 100 registros por página).
  * Función de descarga directa en formato CSV UTF-8 con BOM para Excel (`/reportes/reservas/exportar`).
  * Canal de logs dedicado **`reservas`** configurado en `config/logging.php` con registro de auditoría en `storage/logs/reservas.log`.
  * Pestaña de navegación integrada en la barra superior de todos los módulos de reportes y en el desplegable principal `navigation.blade.php`.
* **v2.6.0**:
  * Creado el **Informe de Satisfacción del Usuario (ISU)** en formato PDF (`/reportes/isu`).

  * Réplica del diseño de referencia con 4 secciones principales: Resumen Ejecutivo ISU, Detalle por Criterios, Hallazgos Críticos & Plan de Acción, y Análisis de Tendencia Trimestral.
  * Filtros de período dinámicos: **Semana Actual** (Lunes a Domingo calculado automáticamente), **Quincena** (1 al 15 del mes actual) y **Mensual** (1er al último día del mes).
  * Promedios por criterios calculados a partir de las evaluaciones registradas (`calidad`, `limpieza`, `temperatura`, `atencion`, `presentacion`).
  * Tendencia trimestral basada en el promedio de conversión (`conversion`) de los últimos 4 meses.
  * Canal de logs dedicado **`isu_report`** en `config/logging.php` con archivos en `storage/logs/isu_report.log`.
* **v2.5.0**:
  * Agregada la vista del **Reporte de Encuestas de Satisfacción** (`/reportes/encuestas`) accesible desde el submenú de navegación de Reportes.
  * Carga por defecto del rango de la **Semana Actual** (Lunes a Domingo) al ingresar al menú de encuestas.
  * Tarjetas KPI de resumen: *Total Encuestas*, *Promedio Calificación (Estrellas)*, *Promedio Conversión (%)* y *Promedio Ponderado Interno (%)*.
  * Tabla interactiva con desglose detallado por criterio (Calidad 30%, Limpieza 25%, Temperatura 20%, Atención 15%, Presentación 10%), calificaciones y comentarios.
  * Filtros dinámicos por Búsqueda de Colaborador (Nombre/Nº), Departamento, Estatus, Rango de Fechas y Paginación (25, 50, 75, 100 registros por página).
  * Exportación completa en CSV UTF-8 para Excel (`/reportes/encuestas/exportar`).
  * Trazabilidad registrada en el canal de logs **`encuestas`** (`storage/logs/encuestas.log`).
* **v2.4.0**:
  * Creado el **Módulo de Encuesta de Satisfacción del Comedor** (`/encuesta`) de acceso público.
  * Implementado flujo de validación de comensal por AJAX con alertas interactivas de SweetAlert2.
  * Verificación obligatoria de ingreso previo al comedor en la fecha actual antes de liberar el formulario.
  * Restricción a nivel de controlador y base de datos de 1 encuesta por día por colaborador (`UNIQUE [empleado_id, fecha]`).
  * Sistema de evaluación por 5 estrellas en 5 criterios clave (Calidad, Limpieza, Temperatura, Atención, Presentación).
  * Campos de conversión en porcentaje (`(calificacion / 5) * 100`) y ponderaciones internas estadísticas (30%, 25%, 20%, 15%, 10%).
  * Configurado canal de logs dedicado **`encuestas`** en `config/logging.php` para almacenar la trazabilidad en `storage/logs/encuestas.log`.
* **v2.3.2**:
  * Actualización de horarios (12:30, 13:15, 14:00, 14:45, 15:30) y capacidad máxima de 120 lugares por turno, con horario de Acceso Libre de 3:30 p.m. a 4:30 p.m.
* **v2.3.1**:
  * Optimizado el layout a factores de crecimiento proporcionales **3:5:2 (`30% - 50% - 20%`)** en Flexbox:
    * **Scanner Control Box (`#scanner-card`)**: `lg:flex-[3] min-w-0`.
    * **Status Feedback Panels (`#status-card`)**: `lg:flex-[5] min-w-0`.
    * **Contador de Accesos (`#counter-card`)**: `lg:flex-[2] min-w-0`.
  * Incorporada la propiedad `min-w-0` y ajuste inteligente de espacios `gap-6` para eliminar el scroll horizontal y lograr una perfecta adaptabilidad responsiva en pantallas de cualquier resolución.
* **v2.3.0**:
  * Incorporadas **Burbujas Contador de Registros de Entrada** en la vista del menú comedor (`/comedor`).
  * Agregada la columna **`Nº Entrada`** en la tabla de historial de comidas del día con burbujas responsivas de alto contraste (`#1`, `#42`, `#9999`) diseñadas con tipografía de amplio tamaño y min-width adaptativo para soportar números de hasta 4 dígitos sin deformar la interfaz.
  * Añadida burbuja contador destacada en la tarjeta de **Acceso Autorizado** mostrando el número consecutivo de acceso del día (`#Acceso Hoy`).
  * Creado el canal dedicado de logs **`comedor`** en `config/logging.php` generando trazabilidad diaria en `storage/logs/comedor.log`.
* **v2.2.0**:
  * Agregados los submenús de reportes: **Reporte General** y **Reporte de Visitas**.
  * Creada la vista de **Reporte de Visitas** (`/reportes/visitas`) mostrando detalles de Nombre, Nº Empleado, Correo, Departamento, Puesto, Estatus y el horario exacto de ingreso al comedor.
  * Implementado el filtro por defecto a la **Semana Actual** en el Reporte de Visitas cuando no se aplican filtros de rango de fechas.
  * Añadida la exportación en CSV para el Reporte de Visitas (`/reportes/visitas/exportar`).
  * Configurado el canal dedicado de trazabilidad **`visitas`** en `config/logging.php` generando logs en `storage/logs/visitas.log`.
* **v2.1.0**:
  * Agregado el canal dedicado de logs **`dashboard`** en `config/logging.php` para registrar la actividad de navegación del Dashboard y los eventos de despacho del reporte por correo electrónico (vía interfaz web y comandos Artisan) en `storage/logs/dashboard.log`.
* **v2.0.0**:
  * Incorporado filtro por **Rango de Fechas (`fecha_inicio` y `fecha_fin`)** en el menú de reportes para acotar el conteo de visitas al comedor por colaborador en el período seleccionado.
  * Mantenida la consulta completa sobre el catálogo de colaboradores sin filtrar la lista de empleados por fecha.
  * Añadida la columna **Visitas Comedor** en la tabla principal y una tarjeta informativa con el total de **Visitas Contabilizadas en Rango**.
  * Sincronizada la acotación por rango de fechas en la exportación masiva a CSV.
  * Registrada la trazabilidad completa de consultas y descargas en el canal exclusivo de logs **`reportes`** en `storage/logs/reportes.log`.
* **v1.9.8**:
  * Corregida la estructura del panel de filtros a **2 filas estrictamente separadas**:
    * **Fila 1 (Grupo 1)**: Utiliza una cuadrícula de 3 columnas (`grid grid-cols-1 sm:grid-cols-3`) para alojar horizontalmente los filtros de Búsqueda, Departamento y Estatus.
    * **Fila 2 (Grupo 2)**: Ubicada directamente por debajo de la Fila 1 (con separador `border-t`), aloja en una segunda fila el combo de Registros por Página a la izquierda y los botones de acción (Filtrar y Limpiar) a la derecha.
* **v1.9.7**:
  * Reorganizado el panel de filtros en **2 filas/niveles verticales superpuestos**:
    * **Fila Superior (Grupo 1)**: Aloja los primeros 3 filtros (Búsqueda por Nombre/Nº, Departamento y Estatus) en una fila horizontal equilibrada (`flex flex-row`).
    * **Fila Inferior (Grupo 2)**: Ubicada directamente por debajo del Grupo 1, aloja el selector de Registros por Página y los Botones de Acción (Filtrar y Limpiar) con amplio espacio horizontal.
* **v1.9.6**:
  * Organizado el panel de filtros en **2 subcontenedores Flex (`flex-1`, 50%/50%) con `flex-direction: row`**:
    * **Grupo 1**: Contiene los primeros 3 filtros (Búsqueda por Nombre/Nº, Departamento y Estatus).
    * **Grupo 2**: Contiene el 4º filtro (Registros por Página) y los Botones de Acción (Filtrar y Limpiar), ocupando exactamente el mismo espacio horizontal que el primer grupo.
* **v1.9.5**:
  * Reestructurado el panel de filtros mediante un contenedor Flexbox continuo (`flex flex-col md:flex-row items-end gap-3.5`) para asegurar que todos los campos y botones se mantengan estrictamente en una **sola fila horizontal** continua en pantallas de escritorio y tabletas.
* **v1.9.4**:
  * Normalizada la altura (`h-10`), tipografía y estructura de los 5 controles y etiquetas del **Panel de Filtros de Búsqueda de Empleados** para garantizar una alineación horizontal milimétrica a la misma altura.
* **v1.9.3**:
  * Agregada la barra de paginación tanto al **inicio (parte superior)** como al pie de la tabla de reportes.
  * Incorporado un combo selector para personalizar la cantidad de registros visibles por página con **25 por defecto**, además de opciones de **50, 75 y 100 registros**.
* **v1.9.2**:
  * Removida la columna de la tabla *"Total Visitas"*, manteniendo el ordenamiento descendente basado en volumen de visitas en backend y la paginación a 50 empleados por página.
* **v1.9.1**:
  * Modificada la consulta del menú de reportes para listar a **todos los empleados** con su conteo total de visitas acumuladas.
  * Removidas las columnas de Día/Fecha de visita y Hora de acceso de la tabla de presentación.
  * Mantenida la ordenación descendente por total de visitas (`registros_comedor_count` desc) y la paginación a 50 empleados por página.
* **v1.9.0**:
  * Removido el filtro de rango de fechas del menú de reportes.
  * Configurada la **carga inicial automática de los primeros 50 registros de visitas** ordenados en forma descendente (visitas más recientes primero).
  * Implementada paginación de 50 registros por página para navegación rápida y optimizada.
* **v1.8.2**:
  * Modificado el comportamiento inicial del **Módulo de Reportes de Visitas**: la pantalla inicia en blanco con un aviso guía sin consultar la base de datos hasta que el usuario envíe explícitamente una solicitud de filtro, optimizando el rendimiento de la aplicación.
* **v1.8.1**:
  * Configurado el canal de logs dedicado **`reportes`** en `config/logging.php` generando archivos de trazabilidad `storage/logs/reportes-YYYY-MM-DD.log`.
  * Integrado el registro de auditoría en `ReporteController`, `DashboardController` y el comando de consola `SendDashboardReportCommand` para auditar accesos, filtros consultados, volúmenes de exportación CSV y correos despachados.
* **v1.9.0**:
  * **Sistema de Validación y Gestión de Roles de Usuario**:
    * Creada la tabla `roles` con 3 niveles jerárquicos: **Rol 1 (Super Admin)**, **Rol 2 (Admin)** y **Rol 3 (Usuario)**.
    * Agregado el campo `role_id` a la tabla `users` con relación de llave foránea.
  * **Gestión Dinámica de Menús y Submenús por Rol**:
    * Creadas las tablas `menus` y `menu_role` para la asignación configurable de permisos de visibilidad de menús y submenús.
    * El **Super Admin (Rol 1)** posee visibilidad total e irrestricta de todos los menús y submenús del sistema, y es el único usuario con permisos para asignar qué roles pueden visualizar cada menú o submenú.
    * Actualizada la plantilla de navegación (`navigation.blade.php`) para renderizar dinámicamente menús y submenús según los permisos de rol del usuario autenticado mediante `Menu::getForUser()`.
  * **Estructura de Submenús en "Gestión de Menús y Roles"**:
    * Creado el submenú **"Asignación de Visibilidad de Menús y Submenús por Rol"** (`admin.menu-roles.menus`) para administrar la matriz de visibilidad.
    * Creado el submenú **"Asignación de Roles a Usuarios Registrados"** (`admin.menu-roles.users`) para la asignación de nivel de rol a cada usuario.
  * **Paginación Configurable de Usuarios Registrados**:
    * Implementada paginación en la vista de asignación de usuarios con selector dinámico de límite por página: **15 (por defecto), 25, 50 y 100 registros**, preservando la navegación y parámetros GET.
  * **Middleware de Autorización por Rol**:
    * Creado el middleware `CheckRole` (`role`) y registrado su alias en `bootstrap/app.php` para proteger rutas administrativas y verificar privilegios de acceso.
  * **Trazabilidad y Canal de Log Propio (`roles`)**:
    * Configurado el canal de log dedicado `'roles'` en `config/logging.php` que almacena registros diarios en `storage/logs/roles.log`.
    * Auditoría automática de cambios en permisos de menús, reasignaciones de rol a usuarios y accesos denegados por restricciones de seguridad.
* **v1.8.0**:
  * Creado el **Módulo de Reportes de Visitas** (`/reportes`) accesible mediante una nueva opción en el menú de navegación principal.
  * Implementada la **Exportación a CSV** por demanda (`/reportes/exportar`) integrando codificación UTF-8 BOM para compatibilidad directa con Excel. El archivo descargado incluye todos los datos del empleado (Número, Nombre, Correo, Departamento, Puesto, Estatus) acompañados del Día de la Semana, Fecha y Hora exacta de acceso.
  * Diseñado un panel de filtros avanzados para segmentar la información por **Estatus del Empleado**, **Departamento**, **Nombre/Número de Colaborador** y **Rango de Fechas (Inicio - Fin)**.
* **v1.7.1**:
  * Reemplazado el logotipo por defecto en el componente `<x-application-logo>` por la imagen oficial de **Comedor GILOU** en el encabezado (header) y la barra de navegación.
  * Configurado el **Favicon oficial** en la sección `<head>` de todas las plantillas (`app`, `guest`, `welcome`) utilizando la imagen del logotipo de **Comedor GILOU** para mostrar la marca en las pestañas del navegador web.
  * Ajustado el conteo de la columna **Visitas** en el menú de Empleados para calcular únicamente los accesos al comedor ocurridos durante la **semana actual de Lunes a Viernes**, manteniendo la recuperación completa de todos los empleados registrados.
* **v1.7.0**:
  * Implementado botón de **Exportación a PDF** del Dashboard en tiempo real usando `html2pdf.js`, capturando todas las gráficas de Chart.js y tarjetas KPI en alta definición.
  * Agregado botón y modal interactivo SweetAlert2 para el **Envío / Programación del Reporte del Dashboard por Correo Electrónico**.
  * Creado el Mailable `DashboardReportMail`, la plantilla HTML de correo `emails/dashboard_report.blade.php` y el comando de consola Artisan `dashboard:send-report` para ejecuciones programadas y envío automático recurrente.
* **v1.6.0**:
  * Agregada validación de **Correo Electrónico** obligatorio junto con el número de colaborador para realizar reservaciones.
  * Implementado control de horario de **apertura de reservaciones a las 8:00 a.m.** (bloqueo automático previo).
  * Implementado límite de **corte anticipado de 15 minutos por horario** de reservación (12:15, 13:30, 14:30, 15:30).
  * Integrado el campo `correo` en la gestión de empleados (CRUD, validación de unicidad, importación masiva CSV y plantilla oficial descargable).
* **v1.5.0**:
  * Agregado flujo AJAX preventivo para validar la identidad y el nombre del colaborador antes de registrar.
  * Implementado modal de confirmación y cancelación SweetAlert2 mostrando Nombre, Fecha y Horario en una tarjeta.
  * Añadida validación proactiva de duplicados por AJAX: si el empleado ya reservó hoy, se le muestra una advertencia con su horario registrado y se detiene el flujo.
  * Integrados logs de auditoría en Laravel (`Log::info`, `Log::warning`) en controladores para registrar accesos, búsquedas AJAX y causas de rechazo en `laravel.log`.
  * Resuelto problema de direccionamiento en subcarpetas de producción (error 404) mediante generación de rutas con `route()` helper de Laravel en JS.
* **v1.4.0**:
  * Reservación restringida estrictamente al día actual (remoción del selector de calendario).
  * Añadido un cuarto horario de reservación a las **15:45 p.m.** (Ventana de tolerancia de **15:45 a 17:00**).
  * Establecido un límite estricto de cupo máximo de **180 lugares** por horario.
  * Agregado indicador visual en tiempo real de lugares disponibles por cada horario.
  * Mensaje de éxito de reservación personalizado con SweetAlert2 mostrando detalles completos del colaborador.
* **v1.3.1**:
  * Rediseño visual del formulario de reservaciones (bordes gruesos índigo, cantos redondeados y remoción de líneas divisorias).
  * Implementada validación obligatoria de reservación activa y ventanas de tolerancia de ingreso en el kiosco de comedor.
  * Creado el **Manual de Usuario Final** para el portal de reservaciones de comedor.
* **v1.3.0**:
  * Agregada la funcionalidad de reservaciones de comedor públicas (basado en el diseño de Comedor GILOU).
  * Selección de fecha y horarios rápidos (12:30, 13:45, 14:45) sincronizados con Alpine.js.
  * Restricción de una sola reserva por día por empleado.
* **v1.2.0**:
  * Integración completa de SweetAlert2 para reemplazo de alertas nativas y banners estáticos.
  * Flexibilización de la regla de validación de número de empleado a un máximo de 10 dígitos (en lugar de obligar a 10 dígitos exactos).
* **v1.1.0**:
  * Implementación de logs de auditoría en la gestión de empleados.
* **v1.0.0**:
  * Lanzamiento inicial con control de comedor, importación CSV de empleados y dashboard de estadísticas.
