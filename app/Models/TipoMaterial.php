<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMaterial extends Model
{
    use HasFactory;

    protected $table = 'tipos_material';
    protected $primaryKey = 'id_tipo_material';
    public $timestamps = false;

    protected $fillable = [
        'nombre_material',
        'requiere_color',
        'requiere_cintillo',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'requiere_color' => 'boolean',
            'requiere_cintillo' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación con unidades de envío
     */
    public function unidadesEnvio()
    {
        return $this->hasMany(UnidadEnvio::class, 'id_tipo_material', 'id_tipo_material');
    }
}