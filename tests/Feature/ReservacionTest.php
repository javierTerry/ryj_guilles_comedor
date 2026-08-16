<?php

namespace Tests\Feature;

use App\Models\Empleado;
use App\Models\Reservacion;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReservacionTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Configurar por defecto el modo POC o permitir pruebas en cualquier horario
        config(['app.require_reservation' => false]);
    }

    public function test_permite_a_un_colaborador_activo_registrar_una_reservacion_exitosamente_con_datos_validos(): void
    {
        $empleado = Empleado::create([
            'numero_empleado' => '1001',
            'nombre' => 'Carlos López',
            'correo' => 'carlos@empresa.com',
            'departamento' => 'Sistemas',
            'puesto' => 'Desarrollador',
            'activo' => true,
        ]);

        $payload = [
            'numero_empleado' => '1001',
            'correo' => 'carlos@empresa.com',
            'hora' => '12:30',
        ];

        $response = $this->post(route('reservaciones.store'), $payload);

        $response->assertRedirect(route('reservaciones.create'))
            ->assertSessionHas('success_reservation');

        $this->assertDatabaseHas('reservaciones', [
            'empleado_id' => $empleado->id,
            'fecha' => Carbon::today()->toDateString(),
            'hora' => '12:30',
            'estatus' => 'activa',
        ]);
    }

    public function test_retorna_error_de_validacion_cuando_faltan_campos_requeridos_en_el_formulario(): void
    {
        $response = $this->post(route('reservaciones.store'), []);

        $response->assertSessionHasErrors(['numero_empleado', 'correo', 'hora']);
    }

    public function test_rechaza_la_reservacion_si_el_numero_de_colaborador_o_correo_no_coinciden(): void
    {
        Empleado::create([
            'numero_empleado' => '1002',
            'nombre' => 'Empleado Oficial',
            'correo' => 'oficial@empresa.com',
            'activo' => true,
        ]);

        $payload = [
            'numero_empleado' => '1002',
            'correo' => 'erroneo@empresa.com',
            'hora' => '13:15',
        ];

        $response = $this->post(route('reservaciones.store'), $payload);

        $response->assertSessionHas('error', 'El número de empleado o correo no pertenecen al registro.');
    }

    public function test_bloquea_la_creacion_de_reservaciones_a_colaboradores_inactivos(): void
    {
        Empleado::create([
            'numero_empleado' => '1003',
            'nombre' => 'Empleado Inactivo',
            'correo' => 'inactivo@empresa.com',
            'activo' => false,
        ]);

        $payload = [
            'numero_empleado' => '1003',
            'correo' => 'inactivo@empresa.com',
            'hora' => '14:00',
        ];

        $response = $this->post(route('reservaciones.store'), $payload);

        $response->assertSessionHas('error', 'El empleado se encuentra inactivo. Comuníquese con administración.');
    }

    public function test_impide_registrar_mas_de_una_reservacion_activa_para_el_mismo_colaborador_en_el_mismo_dia(): void
    {
        $empleado = Empleado::create([
            'numero_empleado' => '1004',
            'nombre' => 'Empleado Duplicado',
            'correo' => 'duplicado@empresa.com',
            'activo' => true,
        ]);

        // Crear reservación previa activa hoy
        Reservacion::create([
            'empleado_id' => $empleado->id,
            'fecha' => Carbon::today()->toDateString(),
            'hora' => '12:30',
            'estatus' => 'activa',
        ]);

        // Intento de segunda reserva hoy
        $payload = [
            'numero_empleado' => '1004',
            'correo' => 'duplicado@empresa.com',
            'hora' => '14:45',
        ];

        $response = $this->post(route('reservaciones.store'), $payload);

        $response->assertSessionHas('error');
    }
}
