<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirmaDigital extends Model
{
    use HasFactory;

    protected $table = 'firmas_digitales';
    protected $primaryKey = 'id_firma';
    public $timestamps = true;

    protected $fillable = [
        'id_nota',
        'nombre_completo_firmante',
        'cedula_firmante',
        'cargo_firmante',
        'firma_base64',
        'tipo_firma',
        'estado_anterior',
        'estado_nuevo',
        'ip_address',
        'user_agent',
        'fecha_firma',
    ];

    protected function casts(): array
    {
        return [
            'fecha_firma' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relación con nota de movimiento
     */
    public function nota()
    {
        return $this->belongsTo(NotaMovimiento::class, 'id_nota', 'id_nota');
    }
}
