<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreeBooking extends Model
{
    use HasFactory;

    public const STATUS_ACTIVO = 'activo';
    public const STATUS_APLICADO = 'aplicado';
    public const STATUS_CANCELADO = 'cancelado';

    protected $table = 'free_bookings';

    protected $fillable = [
        'booking_date',
        'created_by',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
        ];
    }

    /**
     * Relación con el usuario creador de la reserva libre.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope para filtrar reservas libres activas.
     */
    public function scopeActivo($query)
    {
        return $query->where('status', self::STATUS_ACTIVO);
    }

    /**
     * Scope para filtrar reservas libres aplicadas.
     */
    public function scopeAplicado($query)
    {
        return $query->where('status', self::STATUS_APLICADO);
    }

    /**
     * Scope para filtrar reservas libres canceladas.
     */
    public function scopeCancelado($query)
    {
        return $query->where('status', self::STATUS_CANCELADO);
    }

    /**
     * Verifica si la reserva libre se encuentra activa.
     */
    public function isActivo(): bool
    {
        return $this->status === self::STATUS_ACTIVO;
    }

    /**
     * Verifica si la reserva libre ya fue aplicada.
     */
    public function isAplicado(): bool
    {
        return $this->status === self::STATUS_APLICADO;
    }

    /**
     * Verifica si la reserva libre se encuentra cancelada.
     */
    public function isCancelado(): bool
    {
        return $this->status === self::STATUS_CANCELADO;
    }
}
