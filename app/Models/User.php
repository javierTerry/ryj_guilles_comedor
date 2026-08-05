<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Relación con el rol asignado al usuario.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Verificar si el usuario es Super Admin (Rol 1).
     */
    public function isSuperAdmin(): bool
    {
        return (int) $this->role_id === Role::SUPER_ADMIN;
    }

    /**
     * Verificar si el usuario es Admin (Rol 2).
     */
    public function isAdmin(): bool
    {
        return (int) $this->role_id === Role::ADMIN;
    }

    /**
     * Verificar si el usuario posee un rol específico por ID o slug.
     */
    public function hasRole($role): bool
    {
        if (is_numeric($role)) {
            return (int) $this->role_id === (int) $role;
        }

        return optional($this->role)->slug === $role;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role_id' => 'integer',
        ];
    }
}
