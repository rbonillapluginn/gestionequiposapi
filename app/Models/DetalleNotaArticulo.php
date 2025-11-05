<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleNotaArticulo extends Model
{
    use HasFactory;

    protected $table = 'detalle_nota_articulos';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_nota',
        'id_articulo',
        'cantidad',
        'id_unidad_envio',
        'observaciones',
    ];

    /**
     * Relación con nota de movimiento
     */
    public function notaMovimiento()
    {
        return $this->belongsTo(NotaMovimiento::class, 'id_nota', 'id_nota');
    }

    /**
     * Relación con artículo
     */
    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'id_articulo', 'id_articulo');
    }

    /**
     * Relación con unidad de envío
     */
    public function unidadEnvio()
    {
        return $this->belongsTo(UnidadEnvio::class, 'id_unidad_envio', 'id_unidad_envio');
    }
}