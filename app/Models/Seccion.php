<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';
    protected $primaryKey = 'id_seccion';
    public $timestamps = false;

    protected $fillable = [
        'nombre_seccion',
        'descripcion',
        'codigo_seccion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación con permisos de secciones
     */
    public function permisos()
    {
        return $this->hasMany(PermisoSeccion::class, 'id_seccion', 'id_seccion');
    }
}