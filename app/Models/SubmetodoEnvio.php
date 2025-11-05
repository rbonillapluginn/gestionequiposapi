<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmetodoEnvio extends Model
{
    use HasFactory;

    protected $table = 'submetodos_envio';
    protected $primaryKey = 'id_submetodo';
    public $timestamps = false;

    protected $fillable = [
        'id_metodo_envio',
        'nombre_submetodo',
        'descripcion',
        'requiere_mensajero',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'requiere_mensajero' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación con método de envío
     */
    public function metodoEnvio()
    {
        return $this->belongsTo(MetodoEnvio::class, 'id_metodo_envio', 'id_metodo_envio');
    }

    /**
     * Relación con notas de movimiento
     */
    public function notasMovimiento()
    {
        return $this->hasMany(NotaMovimiento::class, 'id_submetodo_envio', 'id_submetodo');
    }
}