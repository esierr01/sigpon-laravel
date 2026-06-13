<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'contact',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
