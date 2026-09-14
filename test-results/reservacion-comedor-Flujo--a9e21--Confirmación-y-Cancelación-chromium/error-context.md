# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: reservacion-comedor.spec.js >> Flujo de Reservación de Comedor - Pruebas E2E con Playwright >> 6. Flujo completo: Verificación AJAX, Modal de Confirmación y Cancelación
- Location: tests/e2e/reservacion-comedor.spec.js:158:3

# Error details

```
TimeoutError: locator.click: Timeout 10000ms exceeded.
Call log:
  - waiting for locator('button').filter({ hasText: '3:30 p.m.' })
    - locator resolved to <button disabled type="button" @click="selectedHora = '15:30'" class="bg-gray-100/90 text-gray-400 border-gray-200 cursor-not-allowed" :class="selectedHora === '15:30' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md ring-2 ring-emerald-300' : (true ? 'bg-gray-100/90 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100')">…</button>
  - attempting click action
    2 × waiting for element to be visible, enabled and stable
      - element is not enabled
    - retrying click action
    - waiting 20ms
    2 × waiting for element to be visible, enabled and stable
      - element is not enabled
    - retrying click action
      - waiting 100ms
    19 × waiting for element to be visible, enabled and stable
       - element is not enabled
     - retrying click action
       - waiting 500ms

```

# Page snapshot

```yaml
- generic [ref=e2]:
  - navigation [ref=e3]:
    - generic [ref=e5]:
      - generic [ref=e6]:
        - link [ref=e8] [cursor=pointer]:
          - /url: http://172.17.0.2/comedor/public/dashboard
          - img "Comedor GILOU" [ref=e9]
        - generic [ref=e10]:
          - link "Reservar" [ref=e11] [cursor=pointer]:
            - /url: http://172.17.0.2/comedor/public/reservar
          - link "Cancelar" [ref=e12] [cursor=pointer]:
            - /url: http://172.17.0.2/comedor/public/reservar/cancelar
      - link "Iniciar Sesión" [ref=e14] [cursor=pointer]:
        - /url: http://172.17.0.2/comedor/public/login
  - banner [ref=e15]:
    - heading "Reservación de Comedor" [level=2] [ref=e17]
  - main [ref=e18]:
    - generic [ref=e20]:
      - generic [ref=e21]:
        - link "Reservar" [ref=e22] [cursor=pointer]:
          - /url: http://172.17.0.2/comedor/public/reservar
        - link "Cancelar" [ref=e26] [cursor=pointer]:
          - /url: http://172.17.0.2/comedor/public/reservar/cancelar
      - generic [ref=e30]:
        - generic [ref=e31]:
          - heading "Haz tu reservación" [level=2] [ref=e32]
          - generic [ref=e33]:
            - generic [ref=e34]:
              - generic [ref=e35]: Número de colaborador
              - textbox "Número de colaborador" [ref=e40]:
                - /placeholder: Ingrese su número de colaborador
            - generic [ref=e41]:
              - generic [ref=e42]: Correo electrónico registrado
              - textbox "Correo electrónico registrado" [ref=e47]:
                - /placeholder: ejemplo@correo.com
            - generic [ref=e48]:
              - generic [ref=e49]:
                - generic [ref=e50]: Fecha de reservación
                - generic [ref=e51]: Reserva Libre Activa
              - generic [ref=e53]:
                - 'button "Día Actual Hoy: 13 de September" [ref=e54] [cursor=pointer]':
                  - generic [ref=e58]:
                    - generic [ref=e59]: Día Actual
                    - generic [ref=e60]: "Hoy: 13 de September"
                - button "Reserva Libre 13 de September" [ref=e64] [cursor=pointer]:
                  - generic [ref=e68]:
                    - generic [ref=e69]: Reserva Libre
                    - generic [ref=e70]: 13 de September
                - button "Reserva Libre 15 de September" [ref=e74] [cursor=pointer]:
                  - generic [ref=e78]:
                    - generic [ref=e79]: Reserva Libre
                    - generic [ref=e80]: 15 de September
                - button "Reserva Libre 28 de October" [ref=e81] [cursor=pointer]:
                  - generic [ref=e85]:
                    - generic [ref=e86]: Reserva Libre
                    - generic [ref=e87]: 28 de October
                - button "Reserva Libre 12 de November" [ref=e88] [cursor=pointer]:
                  - generic [ref=e92]:
                    - generic [ref=e93]: Reserva Libre
                    - generic [ref=e94]: 12 de November
                - button "Reserva Libre 4 de December" [ref=e95] [cursor=pointer]:
                  - generic [ref=e99]:
                    - generic [ref=e100]: Reserva Libre
                    - generic [ref=e101]: 4 de December
                - button "Reserva Libre 4 de March" [ref=e102] [cursor=pointer]:
                  - generic [ref=e106]:
                    - generic [ref=e107]: Reserva Libre
                    - generic [ref=e108]: 4 de March
                - button "Reserva Libre 22 de June" [ref=e109] [cursor=pointer]:
                  - generic [ref=e113]:
                    - generic [ref=e114]: Reserva Libre
                    - generic [ref=e115]: 22 de June
                - button "Reserva Libre 5 de July" [ref=e116] [cursor=pointer]:
                  - generic [ref=e120]:
                    - generic [ref=e121]: Reserva Libre
                    - generic [ref=e122]: 5 de July
              - paragraph [ref=e123]: "Reserva libre activa: El selector limita las opciones al día de hoy y a la fecha configurada. Otras fechas no están disponibles."
            - generic [ref=e124]:
              - generic [ref=e125]: Selección rápida de horario
              - generic [ref=e126]:
                - button "12:30 p.m. a 1:00 p.m. 0/120" [disabled] [ref=e127]:
                  - generic [ref=e128]:
                    - text: 12:30 p.m. a
                    - paragraph
                    - text: 1:00 p.m.
                  - generic [ref=e129]: 0/120
                - button "1:15 p.m. a 1:45 p.m 0/140" [disabled] [ref=e130]:
                  - generic [ref=e131]:
                    - text: 1:15 p.m. a
                    - paragraph
                    - text: 1:45 p.m
                  - generic [ref=e132]: 0/140
                - button "2:00 p.m. a 2:30 p.m. 0/140" [disabled] [ref=e133]:
                  - generic [ref=e134]:
                    - text: 2:00 p.m. a
                    - paragraph
                    - text: 2:30 p.m.
                  - generic [ref=e135]: 0/140
                - button "2:45 p.m. a 3:15 p.m. 0/140" [disabled] [ref=e136]:
                  - generic [ref=e137]:
                    - text: 2:45 p.m. a
                    - paragraph
                    - text: 3:15 p.m.
                  - generic [ref=e138]: 0/140
                - button "3:30 p.m. a 4:30 p.m. Acceso libre" [disabled] [ref=e139]:
                  - generic [ref=e140]:
                    - text: 3:30 p.m. a
                    - paragraph
                    - text: 4:30 p.m.
                  - generic [ref=e141]: Acceso libre
            - button "Reservar" [ref=e143] [cursor=pointer]
        - generic [ref=e146]:
          - generic [ref=e147]:
            - img "Comedor Gilou" [ref=e150]
            - heading "Reserva tu lugar en el comedor" [level=3] [ref=e151]
            - paragraph [ref=e152]: Para asegurar una mejor atención y coordinar el servicio de alimentos diariamente, por favor registre su reservación ingresando su número de colaborador y seleccionando el horario de su preferencia. Las reservaciones se realizan exclusivamente para el día de hoy.
            - generic [ref=e153]:
              - generic [ref=e154]: "5 Horarios Disponibles: 12:30 p.m. a 1:00 p.m., 1:15 p.m. a 1:45 p.m., 2:00 p.m. a 2:30 p.m., 2:45 p.m. a 3:15 p.m. y 3:30 p.m. a 4:30 p.m."
              - generic [ref=e159]: "Capacidad: 120 (12:30), 140 (1:15, 2:00 y 2:45) lugares por horario (Acceso libre de 3:30 p.m. a 4:30 p.m.)."
              - generic [ref=e168]:
                - text: Límite de 1 reservación por día por colaborador.
                - paragraph [ref=e169]: Podrás reservar hasta 15 minutos antes de tu horario de comida, sujeto a disponibilidad.
              - generic [ref=e170]: "Cancelaciones y Cambios: Podrás cancelar o cambiar tu horario al menos 30 minutos antes de la hora reservada."
              - generic [ref=e175]: El sistema de reservas estará disponible todos los días a partir de las 8:00 a.m.
              - generic [ref=e180]: El colaborador debe estar registrado y activo en el sistema.
              - generic [ref=e186]: Llega puntual dentro del horario que reservaste. Si no acudes en el horario seleccionado, tu QR quedará inhabilitado. En ese caso, únicamente podrás ingresar en el último horario disponible (3:30 p.m.)
              - generic [ref=e191]: La línea de servicio cerrará al concluir cada turno y permanecerá cerrada durante 15 minutos. Durante ese periodo no habrá acceso (o servicio)
          - generic [ref=e196]: Agradecemos tu apoyo y colaboración para mantener un servicio más ágil, ordenado y eficiente para todos. Para dudas o soporte técnico, favor de contactar a Pamela Martínez.
```

# Test source

```ts
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
  149 |     const swalWarning = page.locator('.swal2-popup');
  150 |     await expect(swalWarning).toBeVisible();
  151 |     await expect(swalWarning.locator('.swal2-title')).toContainText(/ya registrada|Ya tienes una reservación/i);
  152 |     await expect(swalWarning.locator('.swal2-html-container')).toContainText('12:30 p.m.');
  153 | 
  154 |     await page.click('.swal2-confirm');
  155 |     await expect(swalWarning).not.toBeVisible();
  156 |   });
  157 | 
  158 |   test('6. Flujo completo: Verificación AJAX, Modal de Confirmación y Cancelación', async ({ page }) => {
  159 |     // Simular colaborador verificado exitosamente
  160 |     await page.route('**/reservar/empleado/**', async route => {
  161 |       await route.fulfill({
  162 |         status: 200,
  163 |         contentType: 'application/json',
  164 |         body: JSON.stringify({
  165 |           success: true,
  166 |           nombre: 'Carlos López Hernández'
  167 |         })
  168 |       });
  169 |     });
  170 | 
  171 |     await page.goto('reservar');
  172 | 
> 173 |     await page.locator('button', { hasText: '3:30 p.m.' }).click();
      |                                                            ^ TimeoutError: locator.click: Timeout 10000ms exceeded.
  174 |     await page.fill('#numero_empleado', '1002');
  175 |     await page.fill('#correo', 'carlos.lopez@empresa.com');
  176 | 
  177 |     // Disparar envío
  178 |     await page.click('#reservacion-form button[type="submit"]');
  179 | 
  180 |     // Validar despliegue del modal de confirmación con datos del colaborador
  181 |     const modalConfirm = page.locator('.swal2-popup');
  182 |     await expect(modalConfirm).toBeVisible();
  183 |     await expect(modalConfirm.locator('.swal2-title')).toContainText('¿Confirmar Reservación?');
  184 |     await expect(modalConfirm.locator('.swal2-html-container')).toContainText('Carlos López Hernández');
  185 |     await expect(modalConfirm.locator('.swal2-html-container')).toContainText('3:30 p.m.');
  186 | 
  187 |     // Cancelar en el modal de confirmación: el formulario no debe enviarse
  188 |     const btnCancelarModal = modalConfirm.locator('.swal2-cancel');
  189 |     await expect(btnCancelarModal).toBeVisible();
  190 |     await btnCancelarModal.click();
  191 |     await expect(modalConfirm).not.toBeVisible();
  192 |   });
  193 | 
  194 |   test('7. Flujo completo: Confirmación en modal dispara el envío del formulario', async ({ page }) => {
  195 |     // Simular verificación AJAX exitosa
  196 |     await page.route('**/reservar/empleado/**', async route => {
  197 |       await route.fulfill({
  198 |         status: 200,
  199 |         contentType: 'application/json',
  200 |         body: JSON.stringify({
  201 |           success: true,
  202 |           nombre: 'María Elena Salazar'
  203 |         })
  204 |       });
  205 |     });
  206 | 
  207 |     await page.goto('reservar');
  208 | 
  209 |     await page.locator('button', { hasText: '3:30 p.m.' }).click();
  210 |     await page.fill('#numero_empleado', '1003');
  211 |     await page.fill('#correo', 'maria.salazar@empresa.com');
  212 | 
  213 |     await page.click('#reservacion-form button[type="submit"]');
  214 | 
  215 |     const modalConfirm = page.locator('.swal2-popup');
  216 |     await expect(modalConfirm).toBeVisible();
  217 |     await expect(modalConfirm.locator('.swal2-title')).toContainText('¿Confirmar Reservación?');
  218 | 
  219 |     // Confirmar en el modal: debe disparar el envío POST del formulario con los parámetros correctos
  220 |     const [submitRequest] = await Promise.all([
  221 |       page.waitForRequest(req => req.url().includes('reservar') && req.method() === 'POST'),
  222 |       modalConfirm.locator('.swal2-confirm').click()
  223 |     ]);
  224 | 
  225 |     const postData = submitRequest.postData() || '';
  226 |     expect(postData).toContain('hora=15%3A30');
  227 |     expect(postData).toContain('numero_empleado=1003');
  228 |     expect(postData).toContain('correo=maria.salazar%40empresa.com');
  229 |   });
  230 | 
  231 |   test('8. Selector de fecha interactivo con opciones de Reserva Libre', async ({ page }) => {
  232 |     await page.goto('reservar');
  233 | 
  234 |     const dateContainer = page.locator('#reservation-date-picker-container');
  235 |     await expect(dateContainer).toBeVisible();
  236 | 
  237 |     const inputFecha = page.locator('#fecha_reservacion');
  238 |     await expect(inputFecha).toBeAttached();
  239 | 
  240 |     // Si existen botones de fecha (cuando hay reserva libre activa)
  241 |     const dateButtons = dateContainer.locator('[data-date-target]');
  242 |     const count = await dateButtons.count();
  243 | 
  244 |     if (count > 1) {
  245 |       // Verificar que el primer botón sea el día de hoy
  246 |       const btnHoy = dateButtons.first();
  247 |       await expect(btnHoy).toBeVisible();
  248 | 
  249 |       // Hacer clic en la fecha de reserva libre
  250 |       const btnLibre = dateButtons.nth(1);
  251 |       const targetDate = await btnLibre.getAttribute('data-date-target');
  252 | 
  253 |       await Promise.all([
  254 |         page.waitForURL(url => url.searchParams.get('fecha') === targetDate),
  255 |         btnLibre.click()
  256 |       ]);
  257 | 
  258 |       // Verificar que el input de fecha se haya actualizado
  259 |       await expect(page.locator('#fecha_reservacion')).toHaveValue(targetDate);
  260 |     }
  261 |   });
  262 | 
  263 | });
  264 | 
```