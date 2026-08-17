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
        'estatus',
    ];

    /**
     * Scope a query to only include active reservations.
     */
    public function scopeActivas($query)
    {
        return $query->where('estatus', 'activa');
    }

    /**
     * Get the employee that owns the reservation.
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
