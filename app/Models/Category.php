<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Indicamos que no use updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'created_at',
    ];

    // Aseguramos que created_at sea un objeto de tipo Carbon (fecha)
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
