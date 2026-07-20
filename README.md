# Sistema de Control de Comedor y Empleados

Este es un sistema basado en Laravel diseñado para gestionar el registro diario de consumo de alimentos de los empleados, permitiendo un flujo de escaneo rápido y estadísticas avanzadas para administración.

---

## 🚀 Características Principales

### 1. Registro de Comedor (Acceso Público Kiosco)
* **URL Pública:** `/comedor` (Accesible para cualquiera en red local, no requiere inicio de sesión).
* **Flujo de Escaneo Rápido:** Permite registrar accesos digitando el número de empleado o mediante lectores de códigos de barras.
* **Control de Reservación Obligatoria:** Verifica que el empleado cuente con una reservación registrada para la fecha actual antes de permitir el consumo.
* **Validación de Horario Reservado:** Controla que el ingreso ocurra dentro de las ventanas de tolerancia asignadas al horario reservado:
  * Reservación de las **12:30 p.m.** -> Acceso permitido de **12:00 a 13:30**.
  * Reservación de las **13:45 p.m.** -> Acceso permitido de **13:30 a 14:30**.
  * Reservación de las **14:45 p.m.** -> Acceso permitido de **14:30 a 15:45**.
  * Reservación de las **15:45 p.m.** -> Acceso permitido de **15:45 a 17:00**.
* **Control de Duplicados:** Restricción a nivel de base de datos (`UNIQUE [empleado_id, fecha]`) para impedir que un empleado registre más de una comida por día.
* **Alertas Dinámicas:** Integración de SweetAlert2 para mostrar mensajes interactivos de éxito o error al instante (por ejemplo, sin reservación, fuera de horario, empleado inactivo, comida ya registrada o no encontrado).
* **Historial Oculto:** El listado de accesos del día está protegido; solo los administradores logueados pueden verlo.

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

### 4. Módulo de Reportes de Visitas y Exportación a CSV
* **URL Protegida:** `/reportes` (Acceso mediante nuevo enlace en el menú de navegación principal y móvil).
* **Filtros Avanzados y Rango de Fechas:** Permite consultar la nómina de colaboradores y acotar el volumen de sus visitas al comedor filtrando por:
  * **Rango de Fechas de Visitas (`fecha_inicio` y `fecha_fin`)**: Filtra y contabiliza únicamente los accesos ocurridos dentro del período seleccionado, recalculando las métricas sin omitir empleados del listado.
  * **Nombre o Número de Empleado** (búsqueda parcial).
  * **Departamento** (lista desplegable dinámica).
  * **Estatus del Empleado** (Todos / Activos / Inactivos).
* **Tarjeta KPI de Visitas Contabilizadas:** Muestra en el encabezado la suma total de visitas generadas en el rango de fechas especificado (o del histórico general si no se indica rango).
* **Visualización de Visitas en Tabla:** Columna **Visitas Comedor** dedicada en la tabla principal para identificar de un vistazo el volumen de accesos por colaborador en el rango activo.
* **Carga Inicial y Paginación:** Carga por defecto a todos los empleados ordenados por volumen descendente de visitas con paginación configurable (25, 50, 75 o 100 registros por página).
* **Descarga Masiva en CSV:** Generación en tiempo real de archivos CSV codificados en UTF-8 con BOM (para apertura óptima en Microsoft Excel) que incorporan las visitas acotadas por el rango de fecha seleccionado y todos los datos del colaborador.
* **Canal Dedicado de Logs (`reportes`):** Registro de trazabilidad exclusivo en `storage/logs/reportes.log` que captura eventos de consulta, rangos de fechas filtrados, volúmenes de visitas, descargas CSV e IPs de usuario.

### 5. Reservaciones de Comedor (Acceso Público)
* **URL Pública:** `/reservar` (Accesible para cualquiera en red local).
* **Diseño e Interacción:** Inspirado en "Comedor GILOU" con fuentes personalizadas, inputs minimalistas con iconos de Heroicons y selección rápida de horarios (12:30 p.m., 13:45 p.m., 14:45 p.m., 15:45 p.m.) reactiva mediante Alpine.js.
* **Validaciones del Sistema:**
  * Requisito de **Número de Colaborador y Correo Electrónico registrado** obligatorio para validar la identidad antes de agendar.
  * El número de colaborador debe ser numérico y no exceder los 10 dígitos.
  * **Control de Apertura Diaria (8:30 a.m.):** Las reservaciones solo pueden realizarse a partir de las 8:30 a.m. Muestra aviso informativo si se intenta acceder previamente.
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
  * `resources/views/reservaciones/create.blade.php`: Formulario de reservación pública Comedor GILOU.
  * `resources/views/layouts/navigation.blade.php`: Menú de navegación dinámico adaptativo para visitantes y administradores.

---

## 📌 Historial de Versiones

* **v2.1.0 (Actual)**:
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
  * Implementado control de horario de **apertura de reservaciones a las 8:30 a.m.** (bloqueo automático previo).
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
