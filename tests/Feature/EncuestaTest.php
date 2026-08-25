<?php

namespace Tests\Feature;

use App\Models\Empleado;
use App\Models\Encuesta;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EncuestaTest extends TestCase
{
    use DatabaseTransactions;

    public function test_empleado_activo_puede_validar_datos_sin_haber_ingresado_al_comedor(): void
    {
        $empleado = Empleado::create([
            'numero_empleado' => '8801',
            'nombre' => 'Ana Pruebas',
            'correo' => 'ana.pruebas@empresa.com',
            'departamento' => 'Sistemas',
            'puesto' => 'Desarrollador',
            'activo' => true,
        ]);

        $response = $this->postJson(route('encuestas.validar'), [
            'numero_empleado' => '8801',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'empleado' => [
                    'id' => $empleado->id,
                    'numero_empleado' => '8801',
                    'nombre' => 'Ana Pruebas',
                ],
            ]);
    }

    public function test_empleado_activo_puede_guardar_encuesta_sin_registro_de_comedor(): void
    {
        $empleado = Empleado::create([
            'numero_empleado' => '8802',
            'nombre' => 'Carlos Pruebas',
            'correo' => 'carlos.pruebas@empresa.com',
            'departamento' => 'Calidad',
            'puesto' => 'Inspector',
            'activo' => true,
        ]);

        $response = $this->postJson(route('encuestas.store'), [
            'empleado_id' => $empleado->id,
            'calidad_alimentos' => 5,
            'limpieza_higiene' => 4,
            'temperatura_adecuada' => 5,
            'atencion_eficiencia' => 4,
            'presentacion' => 5,
            'comentarios' => 'Excelente servicio y sabor.',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('encuestas', [
            'empleado_id' => $empleado->id,
            'fecha' => Carbon::today()->toDateString(),
            'calidad_alimentos' => 5,
        ]);
    }

    public function test_empleado_no_puede_responder_mas_de_una_encuesta_el_mismo_dia(): void
    {
        $empleado = Empleado::create([
            'numero_empleado' => '8803',
            'nombre' => 'Beatriz Pruebas',
            'correo' => 'beatriz.pruebas@empresa.com',
            'departamento' => 'RH',
            'puesto' => 'Generalista',
            'activo' => true,
        ]);

        Encuesta::create([
            'empleado_id' => $empleado->id,
            'fecha' => Carbon::today()->toDateString(),
            'hora' => '12:00:00',
            'fecha_hora' => Carbon::now(),
            'calidad_alimentos' => 5,
            'limpieza_higiene' => 5,
            'temperatura_adecuada' => 5,
            'atencion_eficiencia' => 5,
            'presentacion' => 5,
            'calificacion' => 5.0,
            'conversion' => 100.0,
            'ponderacion_total' => 100.0,
        ]);

        // Intentar validar nuevamente el mismo día
        $responseValidar = $this->postJson(route('encuestas.validar'), [
            'numero_empleado' => '8803',
        ]);

        $responseValidar->assertStatus(400)
            ->assertJson([
                'success' => false,
                'type' => 'info',
            ]);

        // Intentar guardar nuevamente el mismo día
        $responseStore = $this->postJson(route('encuestas.store'), [
            'empleado_id' => $empleado->id,
            'calidad_alimentos' => 4,
            'limpieza_higiene' => 4,
            'temperatura_adecuada' => 4,
            'atencion_eficiencia' => 4,
            'presentacion' => 4,
        ]);

        $responseStore->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_empleado_inactivo_o_inexistente_es_rechazado(): void
    {
        // Empleado inactivo
        $empleadoInactivo = Empleado::create([
            'numero_empleado' => '8804',
            'nombre' => 'David Inactivo',
            'correo' => 'david.inactivo@empresa.com',
            'departamento' => 'Ventas',
            'puesto' => 'Ejecutivo',
            'activo' => false,
        ]);

        $responseInactivo = $this->postJson(route('encuestas.validar'), [
            'numero_empleado' => '8804',
        ]);

        $responseInactivo->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);

        // Número inexistente
        $responseInexistente = $this->postJson(route('encuestas.validar'), [
            'numero_empleado' => '999999999',
        ]);

        $responseInexistente->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }
}
