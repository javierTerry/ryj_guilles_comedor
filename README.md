# Sistema de Control de Comedor y Empleados

Este es un sistema basado en Laravel diseñado para gestionar el registro diario de consumo de alimentos de los empleados, permitiendo un flujo de escaneo rápido y estadísticas avanzadas para administración.

---

## 🚀 Características Principales

### 1. Registro de Comedor (Acceso Público Kiosco)
* **URL Pública:** `/comedor` (Accesible para cualquiera en red local, no requiere inicio de sesión).
* **Flujo de Escaneo Rápido:** Permite registrar accesos digitando el número de empleado o mediante lectores de códigos de barras.
* **Control de Duplicados:** Restricción a nivel de base de datos (`UNIQUE [empleado_id, fecha]`) para impedir que un empleado registre más de una comida por día.
* **Alertas Dinámicas:** Integración de SweetAlert2 para mostrar mensajes interactivos de éxito o error al instante (por ejemplo, empleado inactivo, comida ya registrada o no encontrado).
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
* **Vistas:**
  * `resources/views/dashboard.blade.php`: Contenedores y scripts de Chart.js para los 4 gráficos.
  * `resources/views/comedor/index.blade.php`: Pantalla del kiosco de escaneo.
  * `resources/views/layouts/navigation.blade.php`: Menú de navegación dinámico adaptativo para visitantes y administradores.
