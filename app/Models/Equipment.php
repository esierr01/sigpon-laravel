<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    const UPDATED_AT = null;

    protected $fillable = [
        'sku',
        'category_id',
        'name',
        'brand_model_id',
        'unit_id',
        'umbral',
        'active',
        'img_url_one',
        'img_url_two',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relaciones inversas (Pertenece a...)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function brandModel()
    {
        return $this->belongsTo(BrandModel::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relaciones directas (Tiene muchos...)
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
    public function movements()
    {
        return $this->hasMany(Movement::class);
    }
}
