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
        Schema::create('estatus_reservaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Insertar los estatus predefinidos para reservaciones
        DB::table('estatus_reservaciones')->insert([
            [
                'id' => 1,
                'nombre' => 'Activa',
                'slug' => 'activa',
                'descripcion' => 'Reservación vigente y activa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nombre' => 'Cancelada',
                'slug' => 'cancelada',
                'descripcion' => 'Reservación cancelada por el colaborador o administrador',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nombre' => 'Pendiente',
                'slug' => 'pendiente',
                'descripcion' => 'Reservación en estado pendiente de confirmación o procesamiento',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estatus_reservaciones');
    }
};
