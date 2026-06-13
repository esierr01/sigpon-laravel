<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'contact',
        'rif',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
