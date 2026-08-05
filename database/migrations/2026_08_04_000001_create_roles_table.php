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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Insertar los 3 roles predefinidos
        DB::table('roles')->insert([
            [
                'id' => 1,
                'nombre' => 'Super Admin',
                'slug' => 'super_admin',
                'descripcion' => 'Super Administrador del sistema con acceso total y permiso exclusivo para asignar roles a menús',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nombre' => 'Admin',
                'slug' => 'admin',
                'descripcion' => 'Administrador con acceso a módulos y reportes asignados',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nombre' => 'Usuario',
                'slug' => 'usuario',
                'descripcion' => 'Usuario estándar del sistema',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->default(3)->after('email')->constrained('roles')->onDelete('cascade');
        });

        // Asignar rol 1 (Super Admin) al primer usuario registrado si existe
        $firstUser = DB::table('users')->orderBy('id', 'asc')->first();
        if ($firstUser) {
            DB::table('users')->where('id', $firstUser->id)->update(['role_id' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('roles');
    }
};
