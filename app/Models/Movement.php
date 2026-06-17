<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use HasFactory;

    protected $table = 'movements';

    const UPDATED_AT = null;

    protected $fillable = [
        'movement_type',
        'equipment_id',
        'supplier_id',
        'origin_id',
        'destination_id',
        'amount',
        'obs',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relaciones inversas
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relaciones personalizadas para los almacenes (se indica la clave foránea explícitamente)
    public function origin()
    {
        return $this->belongsTo(Store::class, 'origin_id');
    }
    public function destination()
    {
        return $this->belongsTo(Store::class, 'destination_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor para mostrar el nombre del tipo de movimiento (útil para las vistas)
    public function getMovementTypeNameAttribute()
    {
        return match ($this->movement_type) {
            1 => 'Compra',
            2 => 'Salida para Instalar',
            3 => 'Traslado',
            4 => 'Ajuste de Inventario',
            default => 'Desconocido',
        };
    }
}
