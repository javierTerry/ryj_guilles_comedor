<?php

namespace Tests\Feature;

use App\Models\Empleado;
use App\Models\Reservacion;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegistroComedorTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.require_reservation' => true]);
    }

    public function test_rechaza_acceso_si_el_colaborador_tiene_una_reservacion_cancelada_en_horario_restringido(): void
    {
        $empleado = Empleado::create([
            'numero_empleado' => '9901',
            'nombre' => 'Juan Cancelado',
            'correo' => 'juan.cancelado@empresa.com',
            'departamento' => 'Operaciones',
            'puesto' => 'Operador',
            'activo' => true,
        ]);

        // Simular que el horario actual es 12:15 p.m.
        Carbon::setTestNow(Carbon::today()->setTime(12, 15));

        // Crear una reservación cancelada para hoy
        Reservacion::create([
            'empleado_id' => $empleado->id,
            'fecha' => Carbon::today()->toDateString(),
            'hora' => '12:30',
            'estatus' => 'cancelada',
        ]);

        $response = $this->post(route('comedor.registrar'), [
            'numero_empleado' => '9901',
        ]);

        $response->assertRedirect(route('comedor.index'))
            ->assertSessionHas('error', "El empleado {$empleado->nombre} (9901) no cuenta con una reservación activa registrada para el día de hoy.");

        Carbon::setTestNow();
    }

    public function test_permite_acceso_si_el_colaborador_cuenta_con_reservacion_activa_en_su_ventana(): void
    {
        $empleado = Empleado::create([
            'numero_empleado' => '9902',
            'nombre' => 'Maria Activa',
            'correo' => 'maria.activa@empresa.com',
            'departamento' => 'Finanzas',
            'puesto' => 'Analista',
            'activo' => true,
        ]);

        // Simular que la hora actual está dentro de la ventana de 12:30 (12:00 a 13:15)
        Carbon::setTestNow(Carbon::today()->setTime(12, 15));

        // Crear reservación activa
        Reservacion::create([
            'empleado_id' => $empleado->id,
            'fecha' => Carbon::today()->toDateString(),
            'hora' => '12:30',
            'estatus' => 'activa',
        ]);

        $response = $this->post(route('comedor.registrar'), [
            'numero_empleado' => '9902',
        ]);

        $response->assertRedirect(route('comedor.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('registro_comedors', [
            'empleado_id' => $empleado->id,
            'fecha' => Carbon::today()->toDateString(),
        ]);

        Carbon::setTestNow();
    }

    public function test_permite_acceso_en_horario_libre_incluso_si_la_reservacion_fue_cancelada(): void
    {
        $empleado = Empleado::create([
            'numero_empleado' => '9903',
            'nombre' => 'Pedro Libre',
            'correo' => 'pedro.libre@empresa.com',
            'departamento' => 'Logística',
            'puesto' => 'Chofer',
            'activo' => true,
        ]);

        // Simular horario de Acceso Libre (3:45 p.m.)
        Carbon::setTestNow(Carbon::today()->setTime(15, 45));

        // Crear una reservación cancelada para hoy
        Reservacion::create([
            'empleado_id' => $empleado->id,
            'fecha' => Carbon::today()->toDateString(),
            'hora' => '12:30',
            'estatus' => 'cancelada',
        ]);

        $response = $this->post(route('comedor.registrar'), [
            'numero_empleado' => '9903',
        ]);

        $response->assertRedirect(route('comedor.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('registro_comedors', [
            'empleado_id' => $empleado->id,
            'fecha' => Carbon::today()->toDateString(),
        ]);

        Carbon::setTestNow();
    }
}
