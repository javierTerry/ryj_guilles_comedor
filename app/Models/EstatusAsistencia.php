<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstatusAsistencia extends Model
{
    use HasFactory;

    protected $table = 'estatus_asistencias';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
    ];
}
