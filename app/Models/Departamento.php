<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';
    protected $primaryKey = 'id_departamento';
    public $timestamps = false;

    protected $fillable = [
        'nombre_departamento',
        'codigo_departamento',
        'descripcion',
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
     * Relación con encargados de tienda
     */
    public function encargadosTienda()
    {
        return $this->hasMany(EncargadoTienda::class, 'id_departamento', 'id_departamento');
    }
}