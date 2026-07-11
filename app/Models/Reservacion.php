<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservacion extends Model
{
    use HasFactory;

    protected $table = 'reservaciones';

    protected $fillable = [
        'empleado_id',
        'fecha',
        'hora',
    ];

    /**
     * Get the employee that owns the reservation.
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
