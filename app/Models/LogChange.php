<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogChange extends Model
{
    use HasFactory;

    // 1. Especificar el nombre de la tabla (ya que no sigue la convención plural en inglés de Laravel "log_changes")
    protected $table = 'log_change';

    // 2. Desactivar los timestamps automáticos (porque solo tenemos created_at)
    public $timestamps = false;

    // 3. Indicar que created_at es un objeto de tipo Carbon (fecha) para poder usar ->format()
    protected $casts = [
        'created_at' => 'datetime',
    ];

    // 4. Campos asignables masivamente
    protected $fillable = [
        'user_id',
        'table',
        'obs',
        'ip',
        'created_at',
    ];

    // 5. Relación inversa: Un log pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
