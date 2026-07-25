<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['numero_empleado', 'nombre', 'departamento', 'puesto', 'correo', 'activo'])]
class Empleado extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Get the records associated with the employee.
     */
    public function registrosComedor()
    {
        return $this->hasMany(RegistroComedor::class);
    }

    /**
     * Get the reservations associated with the employee.
     */
    public function reservaciones()
    {
        return $this->hasMany(Reservacion::class);
    }

    /**
     * Get the surveys associated with the employee.
     */
    public function encuestas()
    {
        return $this->hasMany(Encuesta::class);
    }
}
