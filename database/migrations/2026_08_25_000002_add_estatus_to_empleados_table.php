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
        Schema::table('empleados', function (Blueprint $table) {
            $table->string('estatus', 30)->default('activo')->after('puesto');
        });

        // Sincronizar el valor inicial de estatus con la columna activo existente
        DB::table('empleados')->where('activo', false)->update(['estatus' => 'inactivo']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropColumn('estatus');
        });
    }
};
