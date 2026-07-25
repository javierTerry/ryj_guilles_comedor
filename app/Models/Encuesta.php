<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'empleado_id',
    'fecha',
    'hora',
    'fecha_hora',
    'calidad_alimentos',
    'limpieza_higiene',
    'temperatura_adecuada',
    'atencion_eficiencia',
    'presentacion',
    'calidad_alimentos_conversion',
    'limpieza_higiene_conversion',
    'temperatura_adecuada_conversion',
    'atencion_eficiencia_conversion',
    'presentacion_conversion',
    'calificacion',
    'conversion',
    'ponderacion_total',
    'comentarios',
])]
class Encuesta extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'encuestas';

    /**
     * Cast de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'fecha_hora' => 'datetime',
            'calidad_alimentos' => 'integer',
            'limpieza_higiene' => 'integer',
            'temperatura_adecuada' => 'integer',
            'atencion_eficiencia' => 'integer',
            'presentacion' => 'integer',
            'calidad_alimentos_conversion' => 'float',
            'limpieza_higiene_conversion' => 'float',
            'temperatura_adecuada_conversion' => 'float',
            'atencion_eficiencia_conversion' => 'float',
            'presentacion_conversion' => 'float',
            'calificacion' => 'float',
            'conversion' => 'float',
            'ponderacion_total' => 'float',
        ];
    }

    /**
     * Relación con el empleado.
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
