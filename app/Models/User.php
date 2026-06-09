<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ✅ Agregar 'role' al fillable
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',           // ← Esto estaba faltando
        'active',
        'create_for',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ Usar casts() no $casts (Laravel 12)
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'active' => 'boolean',
            'role' => 'integer',     // Cast a entero
            'create_for' => 'integer',
        ];
    }

    // Constantes para roles
    const ROLE_ADMIN = 1;
    const ROLE_EDITOR = 2;
    const ROLE_VISITOR = 3;

    // Métodos helper
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isEditor()
    {
        return $this->role === self::ROLE_EDITOR;
    }

    public function isVisitor()
    {
        return $this->role === self::ROLE_VISITOR;
    }

    // Relación autorreferencial
    public function createdUsers()
    {
        return $this->hasMany(User::class, 'create_for');
    }

    // Relación: Un usuario fue creado por otro usuario
    public function creator()
    {
        return $this->belongsTo(User::class, 'create_for');
    }
}
