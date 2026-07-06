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
        Schema::create('empleado_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('empleado_id')->nullable()->constrained('empleados')->onDelete('set null');
            $table->string('empleado_numero', 10)->nullable();
            $table->string('empleado_nombre')->nullable();
            $table->string('action'); // 'crear', 'actualizar', 'cambiar_estado', 'importar'
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleado_logs');
    }
};
