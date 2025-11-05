<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoTipoMovimiento extends Model
{
    use HasFactory;

    protected $table = 'permisos_tipos_movimiento';
    protected $primaryKey = 'id_permiso_movimiento';
    public $timestamps = false;

    protected $fillable = [
        'id_nivel_autorizacion',
        'id_tipo_movimiento',
        'puede_ejecutar',
        'requiere_autorizacion',
    ];

    protected function casts(): array
    {
        return [
            'puede_ejecutar' => 'boolean',
            'requiere_autorizacion' => 'boolean',
        ];
    }

    /**
     * Relación con nivel de autorización
     */
    public function nivelAutorizacion()
    {
        return $this->belongsTo(NivelAutorizacion::class, 'id_nivel_autorizacion', 'id_nivel');
    }

    /**
     * Relación con tipo de movimiento
     */
    public function tipoMovimiento()
    {
        return $this->belongsTo(TipoMovimiento::class, 'id_tipo_movimiento', 'id_tipo_movimiento');
    }
}