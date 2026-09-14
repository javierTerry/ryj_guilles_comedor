# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: reservacion-comedor.spec.js >> Flujo de Reservación de Comedor - Pruebas E2E con Playwright >> 2. Validaciones en cliente con SweetAlert2 para campos obligatorios
- Location: tests/e2e/reservacion-comedor.spec.js:40:3

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator: locator('.swal2-popup')
Expected: visible
Timeout: 5000ms
Error: element(s) not found

Call log:
  - Expect "toBeVisible" locator('.swal2-popup') with timeout 5000ms
  - waiting for locator('.swal2-popup')

```

```yaml
- navigation:
  - link "Comedor GILOU":
    - /url: http://172.17.0.2/comedor/public/dashboard
    - img "Comedor GILOU"
  - link "Reservar":
    - /url: http://172.17.0.2/comedor/public/reservar
  - link "Cancelar":
    - /url: http://172.17.0.2/comedor/public/reservar/cancelar
  - link "Iniciar Sesión":
    - /url: http://172.17.0.2/comedor/public/login
- banner:
  - heading "Reservación de Comedor" [level=2]
- main:
  - link "Reservar":
    - /url: http://172.17.0.2/comedor/public/reservar
    - img
    - text: Reservar
  - link "Cancelar":
    - /url: http://172.17.0.2/comedor/public/reservar/cancelar
    - img
    - text: Cancelar
  - heading "Haz tu reservación" [level=2]
  - text: Número de colaborador
  - img
  - textbox "Número de colaborador":
    - /placeholder: Ingrese su número de colaborador
  - text: Correo electrónico registrado
  - img
  - textbox "Correo electrónico registrado":
    - /placeholder: ejemplo@correo.com
  - text: Fecha de reservación Reserva Libre Activa
  - 'button "Día Actual Hoy: 13 de September"':
    - img
    - text: "Día Actual Hoy: 13 de September"
    - img
  - button "Reserva Libre 13 de September":
    - img
    - text: Reserva Libre 13 de September
    - img
  - button "Reserva Libre 15 de September":
    - img
    - text: Reserva Libre 15 de September
  - button "Reserva Libre 28 de October":
    - img
    - text: Reserva Libre 28 de October
  - button "Reserva Libre 12 de November":
    - img
    - text: Reserva Libre 12 de November
  - button "Reserva Libre 4 de March":
    - img
    - text: Reserva Libre 4 de March
  - button "Reserva Libre 5 de July":
    - img
    - text: Reserva Libre 5 de July
  - paragraph: "Reserva libre activa: El selector limita las opciones al día de hoy y a la fecha configurada. Otras fechas no están disponibles."
  - text: Selección rápida de horario
  - button "12:30 p.m. a 1:00 p.m. 0/120" [disabled]:
    - text: 12:30 p.m. a
    - paragraph
    - text: 1:00 p.m. 0/120
  - button "1:15 p.m. a 1:45 p.m 0/140" [disabled]:
    - text: 1:15 p.m. a
    - paragraph
    - text: 1:45 p.m 0/140
  - button "2:00 p.m. a 2:30 p.m. 0/140" [disabled]:
    - text: 2:00 p.m. a
    - paragraph
    - text: 2:30 p.m. 0/140
  - button "2:45 p.m. a 3:15 p.m. 0/140" [disabled]:
    - text: 2:45 p.m. a
    - paragraph
    - text: 3:15 p.m. 0/140
  - button "3:30 p.m. a 4:30 p.m. Acceso libre" [disabled]:
    - text: 3:30 p.m. a
    - paragraph
    - text: 4:30 p.m. Acceso libre
  - button "Reservar":
    - img
    - text: Reservar
  - img "Comedor Gilou"
  - heading "Reserva tu lugar en el comedor" [level=3]
  - paragraph: Para asegurar una mejor atención y coordinar el servicio de alimentos diariamente, por favor registre su reservación ingresando su número de colaborador y seleccionando el horario de su preferencia. Las reservaciones se realizan exclusivamente para el día de hoy.
  - img
  - text: "5 Horarios Disponibles: 12:30 p.m. a 1:00 p.m., 1:15 p.m. a 1:45 p.m., 2:00 p.m. a 2:30 p.m., 2:45 p.m. a 3:15 p.m. y 3:30 p.m. a 4:30 p.m."
  - img
  - text: "Capacidad: 120 (12:30), 140 (1:15, 2:00 y 2:45) lugares por horario (Acceso libre de 3:30 p.m. a 4:30 p.m.)."
  - img
  - text: Límite de 1 reservación por día por colaborador.
  - paragraph: Podrás reservar hasta 15 minutos antes de tu horario de comida, sujeto a disponibilidad.
  - img
  - text: "Cancelaciones y Cambios: Podrás cancelar o cambiar tu horario al menos 30 minutos antes de la hora reservada."
  - img
  - text: El sistema de reservas estará disponible todos los días a partir de las 8:00 a.m.
  - img
  - text: El colaborador debe estar registrado y activo en el sistema.
  - img
  - text: Llega puntual dentro del horario que reservaste. Si no acudes en el horario seleccionado, tu QR quedará inhabilitado. En ese caso, únicamente podrás ingresar en el último horario disponible (3:30 p.m.)
  - img
  - text: La línea de servicio cerrará al concluir cada turno y permanecerá cerrada durante 15 minutos. Durante ese periodo no habrá acceso (o servicio) Agradecemos tu apoyo y colaboración para mantener un servicio más ágil, ordenado y eficiente para todos. Para dudas o soporte técnico, favor de contactar a Pamela Martínez.
```

# Test source

```ts
  1   | import { test, expect } from '@playwright/test';
  2   | import { login } from './helpers/auth.js';
  3   | 
  4   | test.describe('Flujo de Reservación de Comedor - Pruebas E2E con Playwright', () => {
  5   | 
  6   |   test('1. Acceso público y renderizado de la interfaz de reservación', async ({ page }) => {
  7   |     // La vista es pública y accesible sin autenticación
  8   |     await page.goto('reservar');
  9   |     await expect(page).toHaveURL(/.*reservar/);
  10  | 
  11  |     // Encabezado y títulos principales
  12  |     await expect(page.locator('h2', { hasText: 'Haz tu reservación' })).toBeVisible();
  13  | 
  14  |     // Pestañas del submenú de reservaciones
  15  |     await expect(page.locator('[data-testid="tab-reservar"]')).toBeVisible();
  16  |     await expect(page.locator('[data-testid="tab-cancelar"]')).toBeVisible();
  17  | 
  18  |     // Elementos del formulario
  19  |     const form = page.locator('#reservacion-form');
  20  |     await expect(form).toBeVisible();
  21  |     await expect(page.locator('#numero_empleado')).toBeVisible();
  22  |     await expect(page.locator('#correo')).toBeVisible();
  23  |     await expect(page.locator('#reservation-date-picker-container')).toBeVisible();
  24  | 
  25  |     // Botones de selección rápida de horarios
  26  |     await expect(page.locator('button', { hasText: '12:30 p.m.' })).toBeVisible();
  27  |     await expect(page.locator('button', { hasText: '1:15 p.m.' })).toBeVisible();
  28  |     await expect(page.locator('button', { hasText: '2:00 p.m.' })).toBeVisible();
  29  |     await expect(page.locator('button', { hasText: '2:45 p.m.' })).toBeVisible();
  30  |     await expect(page.locator('button', { hasText: '3:30 p.m.' })).toBeVisible();
  31  | 
  32  |     // Botón principal de confirmación
  33  |     const submitBtn = form.locator('button[type="submit"]', { hasText: 'Reservar' });
  34  |     await expect(submitBtn).toBeVisible();
  35  | 
  36  |     // Panel informativo de reglas y horarios
  37  |     await expect(page.locator('#panel-informativo')).toBeVisible();
  38  |   });
  39  | 
  40  |   test('2. Validaciones en cliente con SweetAlert2 para campos obligatorios', async ({ page }) => {
  41  |     await page.goto('reservar');
  42  |     const form = page.locator('#reservacion-form');
  43  |     const submitBtn = form.locator('button[type="submit"]', { hasText: 'Reservar' });
  44  | 
  45  |     // A. Validar que exija horario si se intenta enviar vacío
  46  |     await submitBtn.click();
  47  |     const swalModal = page.locator('.swal2-popup');
> 48  |     await expect(swalModal).toBeVisible();
      |                             ^ Error: expect(locator).toBeVisible() failed
  49  |     await expect(swalModal.locator('.swal2-title')).toContainText('Horario requerido');
  50  |     await page.click('.swal2-confirm');
  51  |     await expect(swalModal).not.toBeVisible();
  52  | 
  53  |     // B. Seleccionar horario pero dejar número de empleado vacío
  54  |     const btn1230 = page.locator('button', { hasText: '12:30 p.m.' });
  55  |     if (await btn1230.isEnabled()) {
  56  |       await btn1230.click();
  57  |     } else {
  58  |       await page.locator('button', { hasText: '3:30 p.m.' }).click();
  59  |     }
  60  | 
  61  |     await submitBtn.click();
  62  |     await expect(swalModal).toBeVisible();
  63  |     await expect(swalModal.locator('.swal2-title')).toContainText('Número requerido');
  64  |     await page.click('.swal2-confirm');
  65  |     await expect(swalModal).not.toBeVisible();
  66  | 
  67  |     // C. Llenar número de empleado pero dejar correo vacío
  68  |     await page.fill('#numero_empleado', '1001');
  69  |     await submitBtn.click();
  70  |     await expect(swalModal).toBeVisible();
  71  |     await expect(swalModal.locator('.swal2-title')).toContainText('Correo requerido');
  72  |     await page.click('.swal2-confirm');
  73  |     await expect(swalModal).not.toBeVisible();
  74  |   });
  75  | 
  76  |   test('3. Interacción reactiva y selección de horarios en la UI', async ({ page }) => {
  77  |     await page.goto('reservar');
  78  | 
  79  |     const inputHora = page.locator('input[name="hora"]');
  80  | 
  81  |     // Selección de horario 1:15 p.m.
  82  |     const btn1315 = page.locator('button', { hasText: '1:15 p.m.' });
  83  |     if (await btn1315.isEnabled()) {
  84  |       await btn1315.click();
  85  |       await expect(inputHora).toHaveValue('13:15');
  86  |       await expect(btn1315).toHaveClass(/bg-indigo-600/);
  87  |     }
  88  | 
  89  |     // Selección de horario de acceso libre 3:30 p.m.
  90  |     const btn1530 = page.locator('button', { hasText: '3:30 p.m.' });
  91  |     await expect(btn1530).toBeVisible();
  92  |     await btn1530.click();
  93  |     await expect(inputHora).toHaveValue('15:30');
  94  |     await expect(btn1530).toHaveClass(/bg-emerald-600/);
  95  |   });
  96  | 
  97  |   test('4. Validación preventiva de colaborador inexistente mediante AJAX', async ({ page }) => {
  98  |     await page.goto('reservar');
  99  | 
  100 |     // Seleccionar horario libre
  101 |     await page.locator('button', { hasText: '3:30 p.m.' }).click();
  102 | 
  103 |     // Llenar datos de empleado inexistente
  104 |     await page.fill('#numero_empleado', '9999999');
  105 |     await page.fill('#correo', 'noexiste@empresa.com');
  106 | 
  107 |     // Enviar y esperar llamada AJAX
  108 |     const [ajaxResponse] = await Promise.all([
  109 |       page.waitForResponse(res => res.url().includes('reservar/empleado/9999999')),
  110 |       page.click('#reservacion-form button[type="submit"]')
  111 |     ]);
  112 | 
  113 |     expect(ajaxResponse.status()).toBe(200);
  114 | 
  115 |     // Debe mostrar SweetAlert2 de error de validación
  116 |     const swalError = page.locator('.swal2-popup');
  117 |     await expect(swalError).toBeVisible();
  118 |     await expect(swalError.locator('.swal2-title')).toContainText('Error de validación');
  119 |     await expect(swalError.locator('.swal2-html-container')).toContainText('no pertenecen al registro');
  120 | 
  121 |     await page.click('.swal2-confirm');
  122 |     await expect(swalError).not.toBeVisible();
  123 |   });
  124 | 
  125 |   test('5. Detección de colaborador con reservación duplicada activa', async ({ page }) => {
  126 |     // Interceptar la petición de verificación de colaborador para simular duplicado
  127 |     await page.route('**/reservar/empleado/**', async route => {
  128 |       await route.fulfill({
  129 |         status: 200,
  130 |         contentType: 'application/json',
  131 |         body: JSON.stringify({
  132 |           success: false,
  133 |           already_reserved: true,
  134 |           hora_reservada: '12:30',
  135 |           message: 'El colaborador ya cuenta con una reservación hoy para el horario de las 12:30 p.m.'
  136 |         })
  137 |       });
  138 |     });
  139 | 
  140 |     await page.goto('reservar');
  141 | 
  142 |     await page.locator('button', { hasText: '3:30 p.m.' }).click();
  143 |     await page.fill('#numero_empleado', '1001');
  144 |     await page.fill('#correo', 'empleado@empresa.com');
  145 | 
  146 |     await page.click('#reservacion-form button[type="submit"]');
  147 | 
  148 |     // Verificar modal de advertencia de duplicidad
```