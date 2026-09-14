import { test, expect } from '@playwright/test';
import { login } from './helpers/auth.js';

test.describe('Flujo de Reservación de Comedor - Pruebas E2E con Playwright', () => {

  test('1. Acceso público y renderizado de la interfaz de reservación', async ({ page }) => {
    // La vista es pública y accesible sin autenticación
    await page.goto('reservar');
    await expect(page).toHaveURL(/.*reservar/);

    // Encabezado y títulos principales
    await expect(page.locator('h2', { hasText: 'Haz tu reservación' })).toBeVisible();

    // Pestañas del submenú de reservaciones
    await expect(page.locator('[data-testid="tab-reservar"]')).toBeVisible();
    await expect(page.locator('[data-testid="tab-cancelar"]')).toBeVisible();

    // Elementos del formulario
    const form = page.locator('#reservacion-form');
    await expect(form).toBeVisible();
    await expect(page.locator('#numero_empleado')).toBeVisible();
    await expect(page.locator('#correo')).toBeVisible();
    await expect(page.locator('#reservation-date-picker-container')).toBeVisible();

    // Botones de selección rápida de horarios
    await expect(page.locator('button', { hasText: '12:30 p.m.' })).toBeVisible();
    await expect(page.locator('button', { hasText: '1:15 p.m.' })).toBeVisible();
    await expect(page.locator('button', { hasText: '2:00 p.m.' })).toBeVisible();
    await expect(page.locator('button', { hasText: '2:45 p.m.' })).toBeVisible();
    await expect(page.locator('button', { hasText: '3:30 p.m.' })).toBeVisible();

    // Botón principal de confirmación
    const submitBtn = form.locator('button[type="submit"]', { hasText: 'Reservar' });
    await expect(submitBtn).toBeVisible();

    // Panel informativo de reglas y horarios
    await expect(page.locator('#panel-informativo')).toBeVisible();
  });

  test('2. Validaciones en cliente con SweetAlert2 para campos obligatorios', async ({ page }) => {
    await page.goto('reservar');
    const form = page.locator('#reservacion-form');
    const submitBtn = form.locator('button[type="submit"]', { hasText: 'Reservar' });

    // A. Validar que exija horario si se intenta enviar vacío
    await submitBtn.click();
    const swalModal = page.locator('.swal2-popup');
    await expect(swalModal).toBeVisible();
    await expect(swalModal.locator('.swal2-title')).toContainText('Horario requerido');
    await page.click('.swal2-confirm');
    await expect(swalModal).not.toBeVisible();

    // B. Seleccionar horario pero dejar número de empleado vacío
    const btn1230 = page.locator('button', { hasText: '12:30 p.m.' });
    if (await btn1230.isEnabled()) {
      await btn1230.click();
    } else {
      await page.locator('button', { hasText: '3:30 p.m.' }).click();
    }

    await submitBtn.click();
    await expect(swalModal).toBeVisible();
    await expect(swalModal.locator('.swal2-title')).toContainText('Número requerido');
    await page.click('.swal2-confirm');
    await expect(swalModal).not.toBeVisible();

    // C. Llenar número de empleado pero dejar correo vacío
    await page.fill('#numero_empleado', '1001');
    await submitBtn.click();
    await expect(swalModal).toBeVisible();
    await expect(swalModal.locator('.swal2-title')).toContainText('Correo requerido');
    await page.click('.swal2-confirm');
    await expect(swalModal).not.toBeVisible();
  });

  test('3. Interacción reactiva y selección de horarios en la UI', async ({ page }) => {
    await page.goto('reservar');

    const inputHora = page.locator('input[name="hora"]');

    // Selección de horario 1:15 p.m.
    const btn1315 = page.locator('button', { hasText: '1:15 p.m.' });
    if (await btn1315.isEnabled()) {
      await btn1315.click();
      await expect(inputHora).toHaveValue('13:15');
      await expect(btn1315).toHaveClass(/bg-indigo-600/);
    }

    // Selección de horario de acceso libre 3:30 p.m.
    const btn1530 = page.locator('button', { hasText: '3:30 p.m.' });
    await expect(btn1530).toBeVisible();
    await btn1530.click();
    await expect(inputHora).toHaveValue('15:30');
    await expect(btn1530).toHaveClass(/bg-emerald-600/);
  });

  test('4. Validación preventiva de colaborador inexistente mediante AJAX', async ({ page }) => {
    await page.goto('reservar');

    // Seleccionar horario libre
    await page.locator('button', { hasText: '3:30 p.m.' }).click();

    // Llenar datos de empleado inexistente
    await page.fill('#numero_empleado', '9999999');
    await page.fill('#correo', 'noexiste@empresa.com');

    // Enviar y esperar llamada AJAX
    const [ajaxResponse] = await Promise.all([
      page.waitForResponse(res => res.url().includes('reservar/empleado/9999999')),
      page.click('#reservacion-form button[type="submit"]')
    ]);

    expect(ajaxResponse.status()).toBe(200);

    // Debe mostrar SweetAlert2 de error de validación
    const swalError = page.locator('.swal2-popup');
    await expect(swalError).toBeVisible();
    await expect(swalError.locator('.swal2-title')).toContainText('Error de validación');
    await expect(swalError.locator('.swal2-html-container')).toContainText('no pertenecen al registro');

    await page.click('.swal2-confirm');
    await expect(swalError).not.toBeVisible();
  });

  test('5. Detección de colaborador con reservación duplicada activa', async ({ page }) => {
    // Interceptar la petición de verificación de colaborador para simular duplicado
    await page.route('**/reservar/empleado/**', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          success: false,
          already_reserved: true,
          hora_reservada: '12:30',
          message: 'El colaborador ya cuenta con una reservación hoy para el horario de las 12:30 p.m.'
        })
      });
    });

    await page.goto('reservar');

    await page.locator('button', { hasText: '3:30 p.m.' }).click();
    await page.fill('#numero_empleado', '1001');
    await page.fill('#correo', 'empleado@empresa.com');

    await page.click('#reservacion-form button[type="submit"]');

    // Verificar modal de advertencia de duplicidad
    const swalWarning = page.locator('.swal2-popup');
    await expect(swalWarning).toBeVisible();
    await expect(swalWarning.locator('.swal2-title')).toContainText(/ya registrada|Ya tienes una reservación/i);
    await expect(swalWarning.locator('.swal2-html-container')).toContainText('12:30 p.m.');

    await page.click('.swal2-confirm');
    await expect(swalWarning).not.toBeVisible();
  });

  test('6. Flujo completo: Verificación AJAX, Modal de Confirmación y Cancelación', async ({ page }) => {
    // Simular colaborador verificado exitosamente
    await page.route('**/reservar/empleado/**', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          success: true,
          nombre: 'Carlos López Hernández'
        })
      });
    });

    await page.goto('reservar');

    await page.locator('button', { hasText: '3:30 p.m.' }).click();
    await page.fill('#numero_empleado', '1002');
    await page.fill('#correo', 'carlos.lopez@empresa.com');

    // Disparar envío
    await page.click('#reservacion-form button[type="submit"]');

    // Validar despliegue del modal de confirmación con datos del colaborador
    const modalConfirm = page.locator('.swal2-popup');
    await expect(modalConfirm).toBeVisible();
    await expect(modalConfirm.locator('.swal2-title')).toContainText('¿Confirmar Reservación?');
    await expect(modalConfirm.locator('.swal2-html-container')).toContainText('Carlos López Hernández');
    await expect(modalConfirm.locator('.swal2-html-container')).toContainText('3:30 p.m.');

    // Cancelar en el modal de confirmación: el formulario no debe enviarse
    const btnCancelarModal = modalConfirm.locator('.swal2-cancel');
    await expect(btnCancelarModal).toBeVisible();
    await btnCancelarModal.click();
    await expect(modalConfirm).not.toBeVisible();
  });

  test('7. Flujo completo: Confirmación en modal dispara el envío del formulario', async ({ page }) => {
    // Simular verificación AJAX exitosa
    await page.route('**/reservar/empleado/**', async route => {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          success: true,
          nombre: 'María Elena Salazar'
        })
      });
    });

    await page.goto('reservar');

    await page.locator('button', { hasText: '3:30 p.m.' }).click();
    await page.fill('#numero_empleado', '1003');
    await page.fill('#correo', 'maria.salazar@empresa.com');

    await page.click('#reservacion-form button[type="submit"]');

    const modalConfirm = page.locator('.swal2-popup');
    await expect(modalConfirm).toBeVisible();
    await expect(modalConfirm.locator('.swal2-title')).toContainText('¿Confirmar Reservación?');

    // Confirmar en el modal: debe disparar el envío POST del formulario con los parámetros correctos
    const [submitRequest] = await Promise.all([
      page.waitForRequest(req => req.url().includes('reservar') && req.method() === 'POST'),
      modalConfirm.locator('.swal2-confirm').click()
    ]);

    const postData = submitRequest.postData() || '';
    expect(postData).toContain('hora=15%3A30');
    expect(postData).toContain('numero_empleado=1003');
    expect(postData).toContain('correo=maria.salazar%40empresa.com');
  });

  test('8. Selector de fecha interactivo con opciones de Reserva Libre', async ({ page }) => {
    await page.goto('reservar');

    const dateContainer = page.locator('#reservation-date-picker-container');
    await expect(dateContainer).toBeVisible();

    const inputFecha = page.locator('#fecha_reservacion');
    await expect(inputFecha).toBeAttached();

    // Si existen botones de fecha (cuando hay reserva libre activa)
    const dateButtons = dateContainer.locator('[data-date-target]');
    const count = await dateButtons.count();

    if (count > 1) {
      // Verificar que el primer botón sea el día de hoy
      const btnHoy = dateButtons.first();
      await expect(btnHoy).toBeVisible();

      // Hacer clic en la fecha de reserva libre
      const btnLibre = dateButtons.nth(1);
      const targetDate = await btnLibre.getAttribute('data-date-target');

      await Promise.all([
        page.waitForURL(url => url.searchParams.get('fecha') === targetDate),
        btnLibre.click()
      ]);

      // Verificar que el input de fecha se haya actualizado
      await expect(page.locator('#fecha_reservacion')).toHaveValue(targetDate);
    }
  });

});
