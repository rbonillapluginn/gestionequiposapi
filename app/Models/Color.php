<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $table = 'colores';
    protected $primaryKey = 'id_color';
    public $timestamps = false;

    protected $fillable = [
        'nombre_color',
        'codigo_hex',
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
        return $this->hasMany(UnidadEnvio::class, 'id_color', 'id_color');
    }
}