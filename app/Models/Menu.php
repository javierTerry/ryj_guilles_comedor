<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'route_name',
        'icon',
        'parent_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function submenus(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->where('is_active', true)->orderBy('order', 'asc');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'menu_role')->withTimestamps();
    }

    /**
     * Obtener el listado de menús y submenús permitidos según el rol del usuario.
     * El Rol 1 (Super Admin) siempre verá todos los menús activos.
     */
    public static function getForUser(?User $user)
    {
        if (!$user) {
            return collect();
        }

        // El Super Admin (Rol 1) tiene acceso irrestricto a todos los menús activos
        if ($user->isSuperAdmin()) {
            return static::whereNull('parent_id')
                ->where('is_active', true)
                ->with(['submenus' => function ($q) {
                    $q->orderBy('order', 'asc');
                }])
                ->orderBy('order', 'asc')
                ->get();
        }

        $roleId = $user->role_id;

        return static::whereNull('parent_id')
            ->where('is_active', true)
            ->where(function ($query) use ($roleId) {
                $query->whereHas('roles', function ($q) use ($roleId) {
                    $q->where('roles.id', $roleId);
                })->orWhereHas('submenus', function ($subQuery) use ($roleId) {
                    $subQuery->where('is_active', true)->whereHas('roles', function ($q) use ($roleId) {
                        $q->where('roles.id', $roleId);
                    });
                });
            })
            ->with(['submenus' => function ($q) use ($roleId) {
                $q->where('is_active', true)
                  ->whereHas('roles', function ($rq) use ($roleId) {
                      $rq->where('roles.id', $roleId);
                  })
                  ->orderBy('order', 'asc');
            }])
            ->orderBy('order', 'asc')
            ->get();
    }
}
