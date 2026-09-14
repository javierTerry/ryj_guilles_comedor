import { test, expect } from '@playwright/test';
import { login } from './helpers/auth.js';

test.describe('Módulo de Reserva Libre - Pruebas E2E con Playwright', () => {

  test('1. Redirección al login para usuarios no autenticados', async ({ page }) => {
    await page.goto('reservas/libres');
    await expect(page).toHaveURL(/.*login/);
  });

  test('2. Visibilidad de la pestaña "Reserva Libre" en el menú de reservaciones', async ({ page }) => {
    // Iniciar sesión como administrador
    await login(page);

    // Visitar la vista principal de reservaciones
    await page.goto('reservar');
    await expect(page).toHaveURL(/.*reservar/);

    // Verificar presencia de las 3 pestañas
    await expect(page.locator('[data-testid="tab-reservar"]')).toBeVisible();
    await expect(page.locator('[data-testid="tab-cancelar"]')).toBeVisible();
    await expect(page.locator('[data-testid="tab-reserva-libre"]')).toBeVisible();

    // Navegar a la pestaña Reserva Libre
    await page.click('[data-testid="tab-reserva-libre"]');
    await expect(page).toHaveURL(/.*reservas\/libres/);
    await expect(page.locator('h2')).toContainText('Gestión de Reservas Libres');
  });

  test('3. Apertura y validación del modal SweetAlert2 con selector de fecha', async ({ page }) => {
    await login(page);
    await page.goto('reservas/libres');

    // Hacer clic en el botón para agregar reserva libre
    const btnAgregar = page.locator('[data-testid="btn-agregar-reserva-libre"]');
    await expect(btnAgregar).toBeVisible();
    await btnAgregar.click();

    // Validar despliegue del modal SweetAlert2
    const swalModal = page.locator('.swal2-popup');
    await expect(swalModal).toBeVisible();
    await expect(swalModal.locator('.swal2-title')).toContainText('Nueva Reserva Libre');

    // Verificar existencia del input de tipo date
    const dateInput = page.locator('.swal2-input[type="date"]');
    await expect(dateInput).toBeVisible();

    // Limpiar input e intentar confirmar para validar mensaje de error
    await dateInput.fill('');
    await page.click('.swal2-confirm');

    // Validar mensaje de validación en SweetAlert2
    const validationMessage = page.locator('.swal2-validation-message');
    await expect(validationMessage).toBeVisible();
    await expect(validationMessage).toContainText('Debes seleccionar una fecha obligatoria.');

    // Cancelar modal
    await page.click('.swal2-cancel');
    await expect(swalModal).not.toBeVisible();
  });

  test('4. Flujo completo: Crear, Cancelar y Reactivar Reserva Libre con SweetAlert2', async ({ page }) => {
    await login(page);
    await page.goto('reservas/libres');

    // Generar una fecha futura única para la prueba con offset aleatorio para evitar colisiones
    const randomDays = 50 + Math.floor(Math.random() * 150);
    const futureDate = new Date();
    futureDate.setDate(futureDate.getDate() + randomDays);
    const dateStr = futureDate.toISOString().split('T')[0]; // YYYY-MM-DD
    const [year, month, day] = dateStr.split('-');
    const formattedDate = `${day}/${month}/${year}`;

    // A. ABRIR MODAL Y CREAR RESERVA LIBRE
    await page.click('[data-testid="btn-agregar-reserva-libre"]');
    await expect(page.locator('.swal2-popup')).toBeVisible();

    // Llenar fecha y enviar
    await page.fill('.swal2-input[type="date"]', dateStr);

    // Esperar petición POST y respuesta exitosa
    const [response] = await Promise.all([
      page.waitForResponse(res => res.url().includes('/reservas/libres') && res.request().method() === 'POST'),
      page.click('.swal2-confirm')
    ]);

    expect(response.status()).toBe(201);
    const resJson = await response.json();
    const bookingId = resJson?.data?.id;

    // Esperar mensaje de éxito en SweetAlert2 y recarga automática
    await expect(page.locator('.swal2-icon-success')).toBeVisible({ timeout: 5000 });
    await page.waitForLoadState('networkidle');

    // B. VERIFICAR QUE APAREZCA EN LA TABLA
    const tabla = page.locator('[data-testid="tabla-reservas-libres"]');
    await expect(tabla).toBeVisible();
    const fila = bookingId 
      ? tabla.locator(`[data-testid="fila-reserva-${bookingId}"]`)
      : tabla.locator(`tr:has-text("${formattedDate}")`).first();
    await expect(fila).toBeVisible();

    // Verificar que su estatus inicial sea Activo
    const badgeActivo = fila.locator('[data-testid^="badge-status-"]');
    await expect(badgeActivo).toContainText('Activo');

    // C. CANCELAR LA RESERVA LIBRE
    const btnCancelar = fila.locator('[data-testid^="btn-cancelar-"]');
    await expect(btnCancelar).toBeVisible();
    await btnCancelar.click();

    // Verificar modal de confirmación de cancelación
    const modalConfirmacion = page.locator('.swal2-popup');
    await expect(modalConfirmacion).toBeVisible();
    await expect(modalConfirmacion.locator('.swal2-title')).toContainText('¿Cancelar Reserva Libre?');

    // Confirmar cancelación
    const [patchCancelResponse] = await Promise.all([
      page.waitForResponse(res => res.url().includes('/status') && res.request().method() === 'PATCH'),
      page.click('.swal2-confirm')
    ]);

    expect(patchCancelResponse.status()).toBe(200);
    await page.waitForLoadState('networkidle');

    // Verificar que el badge ahora sea Cancelado y aparezca botón Reactivar
    const filaActualizada = bookingId
      ? tabla.locator(`[data-testid="fila-reserva-${bookingId}"]`)
      : tabla.locator(`tr:has-text("${formattedDate}")`).first();
    await expect(filaActualizada.locator('[data-testid^="badge-status-"]')).toContainText('Cancelado');
    const btnReactivar = filaActualizada.locator('[data-testid^="btn-reactivar-"]');
    await expect(btnReactivar).toBeVisible();

    // D. REACTIVAR LA RESERVA LIBRE
    await btnReactivar.click();

    // Verificar modal de reactivación
    await expect(page.locator('.swal2-popup')).toBeVisible();
    await expect(page.locator('.swal2-title')).toContainText('¿Reactivar Reserva Libre?');

    // Confirmar reactivación
    const [patchReactivarResponse] = await Promise.all([
      page.waitForResponse(res => res.url().includes('/status') && res.request().method() === 'PATCH'),
      page.click('.swal2-confirm')
    ]);

    expect(patchReactivarResponse.status()).toBe(200);
    await page.waitForLoadState('networkidle');

    // Verificar que vuelva a estar Activo
    const filaReactivada = bookingId
      ? tabla.locator(`[data-testid="fila-reserva-${bookingId}"]`)
      : tabla.locator(`tr:has-text("${formattedDate}")`).first();
    await expect(filaReactivada.locator('[data-testid^="badge-status-"]')).toContainText('Activo');
  });

  test('5. Prevención de fechas duplicadas en estatus activo vía SweetAlert2', async ({ page }) => {
    await login(page);
    await page.goto('reservas/libres');

    // Generar fecha futura para prueba de duplicado con offset aleatorio
    const randomDaysDup = 210 + Math.floor(Math.random() * 100);
    const futureDate = new Date();
    futureDate.setDate(futureDate.getDate() + randomDaysDup);
    const dateStr = futureDate.toISOString().split('T')[0];

    // 1. Crear la primera reserva activa
    await page.click('[data-testid="btn-agregar-reserva-libre"]');
    await page.fill('.swal2-input[type="date"]', dateStr);
    await page.click('.swal2-confirm');
    await page.waitForLoadState('networkidle');

    // 2. Intentar crear nuevamente una reserva para la misma fecha
    await page.click('[data-testid="btn-agregar-reserva-libre"]');
    await page.fill('.swal2-input[type="date"]', dateStr);

    const [dupResponse] = await Promise.all([
      page.waitForResponse(res => res.url().includes('/reservas/libres') && res.request().method() === 'POST'),
      page.click('.swal2-confirm')
    ]);

    // Debe responder 422
    expect(dupResponse.status()).toBe(422);

    // Debe mostrarse el mensaje de error en el modal SweetAlert2
    const validationMessage = page.locator('.swal2-validation-message');
    await expect(validationMessage).toBeVisible();
    await expect(validationMessage).toContainText('Ya existe una reserva libre activa para la fecha seleccionada');
  });

});
