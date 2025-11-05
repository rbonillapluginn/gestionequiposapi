<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogCorreo extends Model
{
    use HasFactory;

    protected $table = 'log_correos';
    protected $primaryKey = 'id_log_correo';
    public $timestamps = false;

    protected $fillable = [
        'id_nota',
        'id_plantilla',
        'destinatarios',
        'asunto',
        'enviado',
        'fecha_envio',
        'error',
    ];

    protected function casts(): array
    {
        return [
            'enviado' => 'boolean',
            'fecha_envio' => 'datetime',
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
     * Relación con plantilla de correo
     */
    public function plantillaCorreo()
    {
        return $this->belongsTo(PlantillaCorreo::class, 'id_plantilla', 'id_plantilla');
    }
}