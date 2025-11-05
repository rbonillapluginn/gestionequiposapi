<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensajero extends Model
{
    use HasFactory;

    protected $table = 'mensajeros';
    protected $primaryKey = 'id_mensajero';
    public $timestamps = false;

    protected $fillable = [
        'nombre_completo',
        'telefono',
        'identificacion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación con notas de movimiento
     */
    public function notasMovimiento()
    {
        return $this->hasMany(NotaMovimiento::class, 'id_mensajero', 'id_mensajero');
    }
}