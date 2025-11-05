<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoEnvio extends Model
{
    use HasFactory;

    protected $table = 'metodos_envio';
    protected $primaryKey = 'id_metodo_envio';
    public $timestamps = false;

    protected $fillable = [
        'nombre_metodo',
        'descripcion',
        'requiere_vehiculo',
        'requiere_mensajero',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'requiere_vehiculo' => 'boolean',
            'requiere_mensajero' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación con submétodos de envío
     */
    public function submetodos()
    {
        return $this->hasMany(SubmetodoEnvio::class, 'id_metodo_envio', 'id_metodo_envio');
    }

    /**
     * Relación con notas de movimiento
     */
    public function notasMovimiento()
    {
        return $this->hasMany(NotaMovimiento::class, 'id_metodo_envio', 'id_metodo_envio');
    }
}