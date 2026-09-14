<?php

namespace Tests\Feature;

use App\Models\FreeBooking;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FreeBookingTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Asegurar que los roles necesarios existen
        Role::firstOrCreate(['id' => Role::SUPER_ADMIN], [
            'nombre' => 'Super Admin',
            'slug' => 'super_admin',
            'descripcion' => 'Super Administrador',
        ]);

        Role::firstOrCreate(['id' => Role::ADMIN], [
            'nombre' => 'Admin',
            'slug' => 'admin',
            'descripcion' => 'Administrador',
        ]);

        Role::firstOrCreate(['id' => Role::USUARIO], [
            'nombre' => 'Usuario',
            'slug' => 'usuario',
            'descripcion' => 'Usuario regular',
        ]);

        $this->superAdmin = User::factory()->create([
            'role_id' => Role::SUPER_ADMIN,
        ]);

        $this->admin = User::factory()->create([
            'role_id' => Role::ADMIN,
        ]);

        $this->regularUser = User::factory()->create([
            'role_id' => Role::USUARIO,
        ]);
    }

    public function test_invitado_es_redirigido_al_login_al_intentar_acceder(): void
    {
        $response = $this->get(route('reservas.libres.index'));
        $response->assertRedirect(route('login'));

        $postResponse = $this->postJson(route('reservas.libres.store'), [
            'booking_date' => Carbon::tomorrow()->toDateString(),
        ]);
        $postResponse->assertUnauthorized();
    }

    public function test_usuario_estandar_no_tiene_permisos_para_gestionar_reservas_libres(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('reservas.libres.index'));
        $response->assertForbidden();

        $postResponse = $this->actingAs($this->regularUser)->postJson(route('reservas.libres.store'), [
            'booking_date' => Carbon::tomorrow()->toDateString(),
        ]);
        $postResponse->assertForbidden();
    }

    public function test_admin_y_super_admin_pueden_ver_la_vista_de_reservas_libres(): void
    {
        $responseAdmin = $this->actingAs($this->admin)->get(route('reservas.libres.index'));
        $responseAdmin->assertOk();
        $responseAdmin->assertViewIs('reservas.libres');
        $responseAdmin->assertSee('Fechas Habilitadas para Reserva Libre');

        $responseSuper = $this->actingAs($this->superAdmin)->get(route('reservas.libres.index'));
        $responseSuper->assertOk();
    }

    public function test_permite_crear_reserva_libre_con_fecha_valida_y_asigna_creador(): void
    {
        $targetDate = Carbon::tomorrow()->toDateString();

        $response = $this->actingAs($this->admin)->postJson(route('reservas.libres.store'), [
            'booking_date' => $targetDate,
        ]);

        $response->assertCreated();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('free_bookings', [
            'booking_date' => $targetDate,
            'created_by' => $this->admin->id,
            'status' => FreeBooking::STATUS_ACTIVO,
        ]);
    }

    public function test_rechaza_creacion_si_falta_la_fecha_o_es_fecha_pasada(): void
    {
        // Sin fecha
        $response = $this->actingAs($this->admin)->postJson(route('reservas.libres.store'), []);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['booking_date']);

        // Fecha pasada
        $pastDate = Carbon::yesterday()->toDateString();
        $responsePast = $this->actingAs($this->admin)->postJson(route('reservas.libres.store'), [
            'booking_date' => $pastDate,
        ]);
        $responsePast->assertUnprocessable();
        $responsePast->assertJsonValidationErrors(['booking_date']);
    }

    public function test_rechaza_duplicados_de_reserva_libre_activa_para_la_misma_fecha(): void
    {
        $targetDate = Carbon::today()->addDays(3)->toDateString();

        // Primera creación exitosa
        FreeBooking::create([
            'booking_date' => $targetDate,
            'created_by' => $this->superAdmin->id,
            'status' => FreeBooking::STATUS_ACTIVO,
        ]);

        // Intento de segunda creación activa para la misma fecha
        $response = $this->actingAs($this->admin)->postJson(route('reservas.libres.store'), [
            'booking_date' => $targetDate,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('Ya existe una reserva libre activa', $response->json('message'));
    }

    public function test_permite_cancelar_una_reserva_libre_activa(): void
    {
        $targetDate = Carbon::today()->addDays(5)->toDateString();

        $booking = FreeBooking::create([
            'booking_date' => $targetDate,
            'created_by' => $this->admin->id,
            'status' => FreeBooking::STATUS_ACTIVO,
        ]);

        $response = $this->actingAs($this->admin)->patchJson(route('reservas.libres.update_status', $booking), [
            'status' => FreeBooking::STATUS_CANCELADO,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('free_bookings', [
            'id' => $booking->id,
            'status' => FreeBooking::STATUS_CANCELADO,
        ]);
    }

    public function test_permite_reactivar_una_reserva_libre_cancelada(): void
    {
        $targetDate = Carbon::today()->addDays(6)->toDateString();

        $booking = FreeBooking::create([
            'booking_date' => $targetDate,
            'created_by' => $this->superAdmin->id,
            'status' => FreeBooking::STATUS_CANCELADO,
        ]);

        $response = $this->actingAs($this->superAdmin)->patchJson(route('reservas.libres.update_status', $booking), [
            'status' => FreeBooking::STATUS_ACTIVO,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('free_bookings', [
            'id' => $booking->id,
            'status' => FreeBooking::STATUS_ACTIVO,
        ]);
    }

    public function test_bloquea_modificacion_si_el_registro_ya_esta_en_estatus_aplicado(): void
    {
        $targetDate = Carbon::today()->addDays(2)->toDateString();

        $booking = FreeBooking::create([
            'booking_date' => $targetDate,
            'created_by' => $this->admin->id,
            'status' => FreeBooking::STATUS_APLICADO,
        ]);

        $response = $this->actingAs($this->admin)->patchJson(route('reservas.libres.update_status', $booking), [
            'status' => FreeBooking::STATUS_CANCELADO,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('ya ha sido aplicada', $response->json('message'));
    }

    public function test_bloquea_reactivacion_si_ya_existe_otra_reserva_libre_activa_en_la_misma_fecha(): void
    {
        $targetDate = Carbon::today()->addDays(4)->toDateString();

        $bookingCancelado = FreeBooking::create([
            'booking_date' => $targetDate,
            'created_by' => $this->admin->id,
            'status' => FreeBooking::STATUS_CANCELADO,
        ]);

        // Otra activa en la misma fecha
        FreeBooking::create([
            'booking_date' => $targetDate,
            'created_by' => $this->superAdmin->id,
            'status' => FreeBooking::STATUS_ACTIVO,
        ]);

        $response = $this->actingAs($this->admin)->patchJson(route('reservas.libres.update_status', $bookingCancelado), [
            'status' => FreeBooking::STATUS_ACTIVO,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('Ya existe otra reserva libre activa', $response->json('message'));
    }

    public function test_pestana_reserva_libre_es_visible_para_admin_en_todas_las_vistas_de_reservas(): void
    {
        // En vista crear
        $responseCreate = $this->actingAs($this->admin)->get(route('reservaciones.create'));
        $responseCreate->assertOk();
        $responseCreate->assertSee('data-testid="tab-reserva-libre"', false);

        // En vista cancelar
        $responseCancel = $this->actingAs($this->admin)->get(route('reservaciones.cancel_view'));
        $responseCancel->assertOk();
        $responseCancel->assertSee('data-testid="tab-reserva-libre"', false);

        // En vista reserva libre
        $responseLibres = $this->actingAs($this->admin)->get(route('reservas.libres.index'));
        $responseLibres->assertOk();
        $responseLibres->assertSee('data-testid="tab-reserva-libre"', false);
        $responseLibres->assertSee('data-testid="btn-agregar-reserva-libre"', false);
        $responseLibres->assertSee('data-testid="tabla-reservas-libres"', false);
    }

    public function test_pestana_reserva_libre_no_es_visible_para_usuario_regular(): void
    {
        $responseCreate = $this->actingAs($this->regularUser)->get(route('reservaciones.create'));
        $responseCreate->assertOk();
        $responseCreate->assertDontSee('data-testid="tab-reserva-libre"', false);

        $responseCancel = $this->actingAs($this->regularUser)->get(route('reservaciones.cancel_view'));
        $responseCancel->assertOk();
        $responseCancel->assertDontSee('data-testid="tab-reserva-libre"', false);
    }
}
