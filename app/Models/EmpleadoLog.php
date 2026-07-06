<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'empleado_id', 'empleado_numero', 'empleado_nombre', 'action', 'details'])]
class EmpleadoLog extends Model
{
    use HasFactory;

    /**
     * Get the system user who made the change.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the employee associated with the log.
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
