<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    use HasFactory;

    protected $table = 'tiendas';
    protected $primaryKey = 'id_tienda';
    public $timestamps = false;

    protected $fillable = [
        'nombre_tienda',
        'codigo_tienda',
        'direccion',
        'telefono',
        'email',
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
     * Relación con encargados de tienda (solo activos)
     */
    public function encargados()
    {
        return $this->hasMany(EncargadoTienda::class, 'id_tienda', 'id_tienda')
                    ->where('activo', true);
    }

    /**
     * Relación con notas de movimiento como origen
     */
    public function notasOrigen()
    {
        return $this->hasMany(NotaMovimiento::class, 'id_tienda_origen', 'id_tienda');
    }

    /**
     * Relación con notas de movimiento como destino
     */
    public function notasDestino()
    {
        return $this->hasMany(NotaMovimiento::class, 'id_tienda_destino', 'id_tienda');
    }
}