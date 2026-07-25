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
        Schema::create('encuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->onDelete('cascade');
            $table->date('fecha');
            $table->time('hora');
            $table->dateTime('fecha_hora');

            // Calificaciones individuales (1 a 5 estrellas)
            $table->unsignedTinyInteger('calidad_alimentos');
            $table->unsignedTinyInteger('limpieza_higiene');
            $table->unsignedTinyInteger('temperatura_adecuada');
            $table->unsignedTinyInteger('atencion_eficiencia');
            $table->unsignedTinyInteger('presentacion');

            // Conversión individual en porcentaje: ((calificacion / 5) * 100)
            $table->decimal('calidad_alimentos_conversion', 5, 2);
            $table->decimal('limpieza_higiene_conversion', 5, 2);
            $table->decimal('temperatura_adecuada_conversion', 5, 2);
            $table->decimal('atencion_eficiencia_conversion', 5, 2);
            $table->decimal('presentacion_conversion', 5, 2);

            // Campos consolidados de evaluación
            $table->decimal('calificacion', 4, 2); // Promedio de 1 a 5
            $table->decimal('conversion', 5, 2);   // ((calificacion / 5) * 100)
            $table->decimal('ponderacion_total', 5, 2); // Promedio Ponderado Interno (30%, 25%, 20%, 15%, 10%)
            $table->text('comentarios')->nullable();

            $table->timestamps();

            // Garantizar 1 encuesta por día por colaborador
            $table->unique(['empleado_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encuestas');
    }
};
