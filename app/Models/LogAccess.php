<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAccess extends Model
{
    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'log_access';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mail',
        'result',
        'obs',
        'created_at',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'result' => 'boolean',
        'created_at' => 'datetime', // Corrección: era 'timestamp'
    ];

    /**
     * Indica si el modelo debe tener marcas de tiempo automáticas.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Scope para filtrar los intentos exitosos.
     */
    public function scopeExitosos($query)
    {
        return $query->where('result', true);
    }

    /**
     * Scope para filtrar los intentos fallidos.
     */
    public function scopeFallidos($query)
    {
        return $query->where('result', false);
    }
}
