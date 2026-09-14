<?php

namespace Tests\Feature;

use App\Models\Empleado;
use App\Models\FreeBooking;
use App\Models\Reservacion;
use App\Models\User;
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

    public function test_vista_muestra_opciones_de_reserva_libre_cuando_existe_una_activa(): void
    {
        $user = User::factory()->create();
        $targetDate = Carbon::tomorrow()->toDateString();

        FreeBooking::create([
            'booking_date' => $targetDate,
            'created_by' => $user->id,
            'status' => FreeBooking::STATUS_ACTIVO,
        ]);

        $response = $this->get(route('reservaciones.create'));

        $response->assertOk();
        $response->assertSee('Reserva Libre Activa');
        $response->assertSee($targetDate);
    }

    public function test_permite_registrar_reservacion_en_fecha_de_reserva_libre_activa(): void
    {
        $user = User::factory()->create();
        $targetDate = Carbon::tomorrow()->toDateString();

        FreeBooking::create([
            'booking_date' => $targetDate,
            'created_by' => $user->id,
            'status' => FreeBooking::STATUS_ACTIVO,
        ]);

        $empleado = Empleado::create([
            'numero_empleado' => '2001',
            'nombre' => 'Ana Ramírez',
            'correo' => 'ana@empresa.com',
            'activo' => true,
        ]);

        $payload = [
            'numero_empleado' => '2001',
            'correo' => 'ana@empresa.com',
            'hora' => '13:15',
            'fecha' => $targetDate,
        ];

        $response = $this->post(route('reservaciones.store'), $payload);

        $response->assertRedirect(route('reservaciones.create', ['fecha' => $targetDate]))
            ->assertSessionHas('success_reservation');

        $this->assertDatabaseHas('reservaciones', [
            'empleado_id' => $empleado->id,
            'fecha' => $targetDate,
            'hora' => '13:15',
            'estatus' => 'activa',
        ]);
    }

    public function test_rechaza_reservacion_si_la_fecha_no_corresponde_a_hoy_ni_a_reserva_libre_activa(): void
    {
        $empleado = Empleado::create([
            'numero_empleado' => '2002',
            'nombre' => 'Luis Morales',
            'correo' => 'luis@empresa.com',
            'activo' => true,
        ]);

        $unauthorizedDate = Carbon::today()->addDays(4)->toDateString();

        $payload = [
            'numero_empleado' => '2002',
            'correo' => 'luis@empresa.com',
            'hora' => '12:30',
            'fecha' => $unauthorizedDate,
        ];

        $response = $this->post(route('reservaciones.store'), $payload);

        $response->assertSessionHasErrors(['fecha']);
        $this->assertDatabaseMissing('reservaciones', [
            'empleado_id' => $empleado->id,
            'fecha' => $unauthorizedDate,
        ]);
    }

    public function test_rechaza_reservacion_en_fecha_de_reserva_libre_cancelada_o_no_activa(): void
    {
        $user = User::factory()->create();
        $targetDate = Carbon::today()->addDays(3)->toDateString();

        FreeBooking::create([
            'booking_date' => $targetDate,
            'created_by' => $user->id,
            'status' => FreeBooking::STATUS_CANCELADO,
        ]);

        $empleado = Empleado::create([
            'numero_empleado' => '2003',
            'nombre' => 'Elena Vega',
            'correo' => 'elena@empresa.com',
            'activo' => true,
        ]);

        $payload = [
            'numero_empleado' => '2003',
            'correo' => 'elena@empresa.com',
            'hora' => '14:00',
            'fecha' => $targetDate,
        ];

        $response = $this->post(route('reservaciones.store'), $payload);

        $response->assertSessionHasErrors(['fecha']);
    }

    public function test_rechaza_reservacion_para_fechas_pasadas(): void
    {
        $empleado = Empleado::create([
            'numero_empleado' => '2004',
            'nombre' => 'Marcos Luna',
            'correo' => 'marcos@empresa.com',
            'activo' => true,
        ]);

        $pastDate = Carbon::yesterday()->toDateString();

        $payload = [
            'numero_empleado' => '2004',
            'correo' => 'marcos@empresa.com',
            'hora' => '12:30',
            'fecha' => $pastDate,
        ];

        $response = $this->post(route('reservaciones.store'), $payload);

        $response->assertSessionHasErrors(['fecha']);
    }
}
