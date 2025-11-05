<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    use HasFactory;

    protected $table = 'tipos_movimiento';
    protected $primaryKey = 'id_tipo_movimiento';
    public $timestamps = false;

    protected $fillable = [
        'nombre_tipo',
        'descripcion',
        'codigo_tipo',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación con permisos de tipos de movimiento
     */
    public function permisos()
    {
        return $this->hasMany(PermisoTipoMovimiento::class, 'id_tipo_movimiento', 'id_tipo_movimiento');
    }

    /**
     * Relación con notas de movimiento
     */
    public function notasMovimiento()
    {
        return $this->hasMany(NotaMovimiento::class, 'id_tipo_movimiento', 'id_tipo_movimiento');
    }
}