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
        Schema::create('estatus_asistencias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Insertar los estatus predefinidos para asistencia al comedor
        DB::table('estatus_asistencias')->insert([
            [
                'id' => 1,
                'nombre' => 'Acudió',
                'slug' => 'acudio',
                'descripcion' => 'El colaborador asistió y registró su consumo en el comedor en la fecha de reservación',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nombre' => 'Pendiente',
                'slug' => 'pendiente',
                'descripcion' => 'El colaborador aún no ha registrado asistencia en el comedor para la fecha de reservación',
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
        Schema::dropIfExists('estatus_asistencias');
    }
};
