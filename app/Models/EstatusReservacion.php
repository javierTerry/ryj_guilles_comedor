<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstatusReservacion extends Model
{
    use HasFactory;

    protected $table = 'estatus_reservaciones';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
    ];
}
