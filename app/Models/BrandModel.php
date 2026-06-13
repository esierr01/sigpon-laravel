<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandModel extends Model
{
    use HasFactory;

    // Especificamos el nombre de la tabla ya que no sigue la convención plural estricta de Laravel
    protected $table = 'brand_models';

    const UPDATED_AT = null;

    protected $fillable = [
        'brand',
        'model',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
