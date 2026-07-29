<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MovementType extends Model
{
    protected $table = 'movement_types';

    protected $fillable = [
        'name',
    ];

    /**
     * Relación: Un tipo de movimiento tiene muchos movimientos
     */
    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class, 'movement_type');
    }
}
