<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    public const SUPER_ADMIN = 1;
    public const ADMIN = 2;
    public const USUARIO = 3;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_role')->withTimestamps();
    }
}
