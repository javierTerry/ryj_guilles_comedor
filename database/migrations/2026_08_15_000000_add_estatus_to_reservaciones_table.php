<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservaciones', function (Blueprint $table) {
            // 1. Crear un índice normal para empleado_id que satisfaga la Foreign Key
            $table->index('empleado_id', 'reservaciones_empleado_id_index');

            // 2. Eliminar restricción de unicidad estricta para permitir historial de reservaciones canceladas
            $table->dropUnique(['empleado_id', 'fecha']);
            
            // 3. Agregar columna de estatus ('activa', 'cancelada')
            $table->string('estatus')->default('activa')->after('hora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservaciones', function (Blueprint $table) {
            $table->dropColumn('estatus');
            $table->unique(['empleado_id', 'fecha']);
            $table->dropIndex('reservaciones_empleado_id_index');
        });
    }
};
