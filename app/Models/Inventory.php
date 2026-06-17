<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    const UPDATED_AT = null;

    protected $fillable = [
        'equipment_id',
        'store_id',
        'stock',
        'last_change',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'last_change' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Relaciones inversas
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
