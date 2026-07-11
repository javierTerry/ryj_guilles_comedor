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
* **Control de Duplicados:** Restricción a nivel de base de datos (`UNIQUE [empleado_id, fecha]`) para impedir que un empleado registre más de una comida por día.
* **Alertas Dinámicas:** Integración de SweetAlert2 para mostrar mensajes interactivos de éxito o error al instante (por ejemplo, sin reservación, fuera de horario, empleado inactivo, comida ya registrada o no encontrado).
* **Historial Oculto:** El listado de accesos del día está protegido; solo los administradores logueados pueden verlo.

### 2. Panel de Estadísticas (Dashboard Administrativo)
* **URL Protegida:** `/dashboard` (Solo para administradores autenticados).
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
* **Importación Masiva:** Subida y procesamiento de listas de empleados mediante archivos CSV con descarga de plantilla oficial integrada.
* **Estado de Empleado:** Switch interactivo para activar o desactivar empleados de forma rápida (un empleado desactivado no podrá registrar comidas).

### 4. Reservaciones de Comedor (Acceso Público)
* **URL Pública:** `/reservar` (Accesible para cualquiera en red local).
* **Diseño e Interacción:** Inspirado en "Comedor GILOU" con fuentes personalizadas, inputs minimalistas con iconos de Heroicons y selección rápida de horarios (12:30 p.m., 13:45 p.m., 14:45 p.m.) reactiva mediante Alpine.js.
* **Validaciones del Sistema:**
  * El número de colaborador debe ser numérico y no exceder los 10 dígitos.
  * El colaborador debe existir previamente en el sistema y estar activo.
  * Límite estricto de una reservación por día por empleado.
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
* **Modelos:**
  * `Empleado.php`: Modelo principal para la gestión de colaboradores.
  * `RegistroComedor.php`: Modelo para los consumos diarios del comedor.
  * `Reservacion.php`: Modelo para las citas y reservaciones de comedor GILOU.
  * `EmpleadoLog.php`: Bitácora de cambios para la auditoría de personal.
* **Vistas:**
  * `resources/views/dashboard.blade.php`: Contenedores y scripts de Chart.js para los 4 gráficos.
  * `resources/views/comedor/index.blade.php`: Pantalla del kiosco de escaneo.
  * `resources/views/reservaciones/create.blade.php`: Formulario de reservación pública Comedor GILOU.
  * `resources/views/layouts/navigation.blade.php`: Menú de navegación dinámico adaptativo para visitantes y administradores.

---

## 📌 Historial de Versiones

* **v1.3.0 (Actual)**:
  * Agregada la funcionalidad de reservaciones de comedor públicas (basado en el diseño de Comedor GILOU).
  * Selección de fecha y horarios rápidos (12:30, 13:45, 14:45) sincronizados con Alpine.js.
  * Restricción de una sola reserva por día por empleado.
  * Implementada validación obligatoria de reservación en el kiosco de comedor.
  * Implementada validación de horario y ventanas de tolerancia de ingreso al comedor.
* **v1.2.0**:
  * Integración completa de SweetAlert2 para reemplazo de alertas nativas y banners estáticos.
  * Flexibilización de la regla de validación de número de empleado a un máximo de 10 dígitos (en lugar de obligar a 10 dígitos exactos).
* **v1.1.0**:
  * Implementación de logs de auditoría en la gestión de empleados.
* **v1.0.0**:
  * Lanzamiento inicial con control de comedor, importación CSV de empleados y dashboard de estadísticas.
