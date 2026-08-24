<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoleMenuTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Garantizar existencia de roles si no fueron migrados en este entorno
        Role::firstOrCreate(
            ['id' => 4],
            [
                'nombre' => 'usuario1',
                'slug' => 'usuario1',
                'descripcion' => 'Rol usuario1 con acceso inicial exclusivamente al módulo de encuestas',
            ]
        );

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $encuestaMenu = Menu::where('route_name', 'encuestas.create')->orWhere('id', 3)->first();
        if ($encuestaMenu) {
            $encuestaMenu->roles()->syncWithoutDetaching([4]);
        }
    }

    public function test_rol_usuario1_posee_constante_y_helper_en_usuario(): void
    {
        $this->assertSame(4, Role::USUARIO1);

        $user = User::factory()->create([
            'role_id' => Role::USUARIO1,
        ]);

        $this->assertTrue($user->isUsuario1());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isSuperAdmin());
    }

    public function test_usuario1_solo_visualiza_menu_de_encuestas_por_defecto(): void
    {
        $user = User::factory()->create([
            'role_id' => Role::USUARIO1,
        ]);

        $menus = Menu::getForUser($user);

        $this->assertNotEmpty($menus);
        $routeNames = $menus->pluck('route_name')->toArray();

        // Debe contener encuestas.create
        $this->assertContains('encuestas.create', $routeNames);

        // No debe contener menus administrativos ni reportes no autorizados
        $this->assertNotContains('admin.menu-roles.menus', $routeNames);
        $this->assertNotContains('empleados.index', $routeNames);
    }

    public function test_super_admin_puede_actualizar_rol_de_un_usuario_a_usuario1(): void
    {
        $superAdmin = User::factory()->create([
            'role_id' => Role::SUPER_ADMIN,
        ]);

        $targetUser = User::factory()->create([
            'role_id' => Role::USUARIO,
        ]);

        $response = $this->actingAs($superAdmin)
            ->patch(route('admin.menu-roles.update-user-role', $targetUser->id), [
                'role_id' => Role::USUARIO1,
            ]);

        $response->assertRedirect(route('admin.menu-roles.users', ['per_page' => 15, 'page' => 1]));
        $response->assertSessionHas('status');

        $this->assertSame(Role::USUARIO1, $targetUser->fresh()->role_id);
    }

    public function test_usuario1_no_puede_acceder_al_panel_de_administracion_de_roles(): void
    {
        $user = User::factory()->create([
            'role_id' => Role::USUARIO1,
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.menu-roles.menus'));

        $response->assertForbidden();
    }
}
