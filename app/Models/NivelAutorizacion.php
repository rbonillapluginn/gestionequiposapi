<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelAutorizacion extends Model
{
    use HasFactory;

    protected $table = 'niveles_autorizacion';
    protected $primaryKey = 'id_nivel';
    public $timestamps = false;

    protected $fillable = [
        'nombre_nivel',
        'descripcion',
        'orden_jerarquico',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_creacion' => 'datetime',
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación con usuarios
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_nivel_autorizacion', 'id_nivel');
    }

    /**
     * Relación con permisos de secciones
     */
    public function permisosSecciones()
    {
        return $this->hasMany(PermisoSeccion::class, 'id_nivel_autorizacion', 'id_nivel');
    }

    /**
     * Relación con permisos de tipos de movimiento
     */
    public function permisosTiposMovimiento()
    {
        return $this->hasMany(PermisoTipoMovimiento::class, 'id_nivel_autorizacion', 'id_nivel');
    }
}