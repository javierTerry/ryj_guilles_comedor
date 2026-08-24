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
        // 1. Insertar el 4to rol: usuario1
        DB::table('roles')->insertOrIgnore([
            'id' => 4,
            'nombre' => 'usuario1',
            'slug' => 'usuario1',
            'descripcion' => 'Rol usuario1 con acceso inicial exclusivamente al módulo de encuestas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Asignar visibilidad por defecto en menu_role: Menú 3 (Encuesta)
        $encuestaMenu = DB::table('menus')->where('route_name', 'encuestas.create')->orWhere('id', 3)->first();
        if ($encuestaMenu) {
            DB::table('menu_role')->insertOrIgnore([
                'menu_id' => $encuestaMenu->id,
                'role_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reasignar usuarios con rol 4 al rol 3 (Usuario) antes de eliminar
        DB::table('users')->where('role_id', 4)->update(['role_id' => 3]);

        // Eliminar permisos asociados en la tabla pivote menu_role
        DB::table('menu_role')->where('role_id', 4)->delete();

        // Eliminar el rol usuario1
        DB::table('roles')->where('id', 4)->orWhere('slug', 'usuario1')->delete();
    }
};
