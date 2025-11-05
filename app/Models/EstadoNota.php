<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoNota extends Model
{
    use HasFactory;

    protected $table = 'estados_nota';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;

    protected $fillable = [
        'nombre_estado',
        'descripcion',
        'orden',
    ];

    /**
     * Relación con notas de movimiento
     */
    public function notasMovimiento()
    {
        return $this->hasMany(NotaMovimiento::class, 'id_estado', 'id_estado');
    }

    /**
     * Relación con historial de estados (estado anterior)
     */
    public function historialEstadoAnterior()
    {
        return $this->hasMany(HistorialEstadoNota::class, 'id_estado_anterior', 'id_estado');
    }

    /**
     * Relación con historial de estados (estado nuevo)
     */
    public function historialEstadoNuevo()
    {
        return $this->hasMany(HistorialEstadoNota::class, 'id_estado_nuevo', 'id_estado');
    }
}