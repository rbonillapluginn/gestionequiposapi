<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoSeccion extends Model
{
    use HasFactory;

    protected $table = 'permisos_secciones';
    protected $primaryKey = 'id_permiso_seccion';
    public $timestamps = false;

    protected $fillable = [
        'id_nivel_autorizacion',
        'id_seccion',
        'puede_leer',
        'puede_crear',
        'puede_modificar',
        'puede_eliminar',
    ];

    protected function casts(): array
    {
        return [
            'puede_leer' => 'boolean',
            'puede_crear' => 'boolean',
            'puede_modificar' => 'boolean',
            'puede_eliminar' => 'boolean',
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
     * Relación con sección
     */
    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'id_seccion', 'id_seccion');
    }
}