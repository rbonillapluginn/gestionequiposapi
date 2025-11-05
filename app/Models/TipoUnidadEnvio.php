<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoUnidadEnvio extends Model
{
    use HasFactory;

    protected $table = 'tipos_unidad_envio';
    protected $primaryKey = 'id_tipo_unidad';
    public $timestamps = false;

    protected $fillable = [
        'nombre_tipo',
        'descripcion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación con unidades de envío
     */
    public function unidadesEnvio()
    {
        return $this->hasMany(UnidadEnvio::class, 'id_tipo_unidad', 'id_tipo_unidad');
    }
}