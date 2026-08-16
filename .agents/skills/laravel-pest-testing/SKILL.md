---
name: laravel-pest-testing
description: Genera suites de pruebas unitarias y de integración completas usando Pest PHP en Laravel 13, asegurando cobertura de casos de éxito, validación y errores de negocio.
---

# Generador de Pruebas con Pest PHP en Laravel 13

## Objetivo
Escribir pruebas automatizadas con Pest PHP limpias, expresivas y exhaustivas que cubran endpoints HTTP, servicios, políticas de acceso y eventos.

---

## 1. Convenciones y Estructura
- **Ubicación:** 
  - Pruebas unitarias en `tests/Unit/`.
  - Pruebas de integración/endpoints en `tests/Feature/`.
- **Database Reset:** Utilizar `uses(Illuminate\Foundation\Testing\RefreshDatabase::class);` en `tests/Pest.php` o dentro del archivo cuando interactúe con base de datos.
- **Factories y Mocking:** Usar siempre Model Factories para preparar datos y `Http::fake()` o `Event::fake()` para dependencias externas.

---

## 2. Matriz de Cobertura Obligatoria por Feature
Al crear pruebas para un endpoint o flujo, la suite debe incluir como mínimo:

1. **Happy Path (Caso de éxito):**
   - Retorno del código HTTP adecuado (`200 OK`, `201 Created`, etc.).
   - Persistencia correcta en base de datos (`assertDatabaseHas`).
   - Formato de respuesta JSON esperado (`assertJsonStructure` o `assertJsonPath`).

2. **Validación (Form Requests):**
   - Envío de payloads vacíos o con tipos inválidos (`assertSessionHasErrors` o `assertJsonValidationErrors`).
   - Validación de unicidad o límites de tamaño.

3. **Autorización y Autenticación:**
   - Intentos de acceso sin autenticar (`actingAs()` omitido -> `401 Unauthorized`).
   - Intentos con roles/permisos no autorizados (`403 Forbidden`).

4. **Reglas de Negocio y Excepciones:**
   - Casos límite (ej. saldo insuficiente, stock agotado, fechas pasadas).

---

## 3. Plantilla de Referencia (Feature Test)

```php
use App\Models\User;
use function Pest\Laravel\{actingAs, postJson, assertDatabaseHas};

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('permite a un usuario autenticado crear un recurso con datos válidos', function () {
    $payload = [
        'title' => 'Nuevo Título',
        'status' => 'active',
    ];

    actingAs($this->user)
        ->postJson(route('recursos.store'), $payload)
        ->assertCreated()
        ->assertJsonPath('data.title', 'Nuevo Título');

    assertDatabaseHas('recursos', [
        'title' => 'Nuevo Título',
        'user_id' => $this->user->id,
    ]);
});

it('retorna error de validación cuando los campos requeridos faltan', function () {
    actingAs($this->user)
        ->postJson(route('recursos.store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

it('bloquea el acceso a usuarios no autenticados', function () {
    postJson(route('recursos.store'), ['title' => 'Test'])
        ->assertUnauthorized();
});