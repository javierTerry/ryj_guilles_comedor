<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('route_name')->nullable();
            $table->string('icon')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['menu_id', 'role_id']);
        });

        // Insertar estructura inicial de menús y submenús
        $menus = [
            ['id' => 1,  'title' => 'Dashboard',                                                'route_name' => 'dashboard',               'icon' => 'chart-bar',       'parent_id' => null, 'order' => 1],
            ['id' => 2,  'title' => 'Comedor',                                                  'route_name' => 'comedor.index',           'icon' => 'utensils',        'parent_id' => null, 'order' => 2],
            ['id' => 3,  'title' => 'Encuesta',                                                 'route_name' => 'encuestas.create',        'icon' => 'clipboard-check', 'parent_id' => null, 'order' => 3],
            ['id' => 4,  'title' => 'Reservar',                                                 'route_name' => 'reservaciones.create',    'icon' => 'calendar',        'parent_id' => null, 'order' => 4],
            ['id' => 5,  'title' => 'Empleados',                                                'route_name' => 'empleados.index',         'icon' => 'users',           'parent_id' => null, 'order' => 5],
            ['id' => 6,  'title' => 'Reportes',                                                 'route_name' => 'reportes.index',          'icon' => 'document-report', 'parent_id' => null, 'order' => 6],
            // Submenús de Reportes
            ['id' => 7,  'title' => 'Reporte General',                                          'route_name' => 'reportes.index',          'icon' => 'table',           'parent_id' => 6,    'order' => 1],
            ['id' => 8,  'title' => 'Reporte de Visitas',                                       'route_name' => 'reportes.visitas',        'icon' => 'user-check',      'parent_id' => 6,    'order' => 2],
            ['id' => 9,  'title' => 'Reporte de Reservas',                                      'route_name' => 'reportes.reservas',       'icon' => 'bookmark',        'parent_id' => 6,    'order' => 3],
            ['id' => 10, 'title' => 'Reporte de Encuestas',                                     'route_name' => 'reportes.encuestas',      'icon' => 'star',            'parent_id' => 6,    'order' => 4],
            ['id' => 11, 'title' => 'Informe ISU (PDF)',                                        'route_name' => 'reportes.isu',            'icon' => 'document-pdf',    'parent_id' => 6,    'order' => 5],
            // Menú contenedor de Administración de Roles y Menús (Exclusivo Super Admin)
            ['id' => 12, 'title' => 'Gestión de Menús y Roles',                                 'route_name' => 'admin.menu-roles.menus',  'icon' => 'shield-check',   'parent_id' => null, 'order' => 7],
            // Submenús de Gestión de Menús y Roles
            ['id' => 13, 'title' => 'Asignación de Visibilidad de Menús y Submenús por Rol',     'route_name' => 'admin.menu-roles.menus',  'icon' => 'eye',             'parent_id' => 12,   'order' => 1],
            ['id' => 14, 'title' => 'Asignación de Roles a Usuarios Registrados',               'route_name' => 'admin.menu-roles.users',  'icon' => 'user-group',      'parent_id' => 12,   'order' => 2],
        ];

        foreach ($menus as $menu) {
            DB::table('menus')->insert(array_merge($menu, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Asignaciones por defecto en menu_role
        $menuRoles = [];

        // Super Admin (Rol 1): Todos los menús (IDs 1-14)
        for ($i = 1; $i <= 14; $i++) {
            $menuRoles[] = ['menu_id' => $i, 'role_id' => 1, 'created_at' => now(), 'updated_at' => now()];
        }

        // Admin (Rol 2): Menús 1 a 11
        for ($i = 1; $i <= 11; $i++) {
            $menuRoles[] = ['menu_id' => $i, 'role_id' => 2, 'created_at' => now(), 'updated_at' => now()];
        }

        // Usuario (Rol 3): Menús 1, 2, 3, 4
        foreach ([1, 2, 3, 4] as $menuId) {
            $menuRoles[] = ['menu_id' => $menuId, 'role_id' => 3, 'created_at' => now(), 'updated_at' => now()];
        }

        DB::table('menu_role')->insert($menuRoles);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_role');
        Schema::dropIfExists('menus');
    }
};
