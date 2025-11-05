<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialEstadoNota extends Model
{
    use HasFactory;

    protected $table = 'historial_estados_nota';
    protected $primaryKey = 'id_historial';
    public $timestamps = false;

    protected $fillable = [
        'id_nota',
        'id_estado_anterior',
        'id_estado_nuevo',
        'id_usuario',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_cambio' => 'datetime',
        ];
    }

    /**
     * Relación con nota de movimiento
     */
    public function notaMovimiento()
    {
        return $this->belongsTo(NotaMovimiento::class, 'id_nota', 'id_nota');
    }

    /**
     * Relación con estado anterior
     */
    public function estadoAnterior()
    {
        return $this->belongsTo(EstadoNota::class, 'id_estado_anterior', 'id_estado');
    }

    /**
     * Relación con estado nuevo
     */
    public function estadoNuevo()
    {
        return $this->belongsTo(EstadoNota::class, 'id_estado_nuevo', 'id_estado');
    }

    /**
     * Relación con usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}