<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';
    protected $primaryKey = 'id_vehiculo';
    public $timestamps = false;

    protected $fillable = [
        'numero_camion',
        'placa',
        'modelo',
        'capacidad_carga',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'capacidad_carga' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación con notas de movimiento
     */
    public function notasMovimiento()
    {
        return $this->hasMany(NotaMovimiento::class, 'id_vehiculo', 'id_vehiculo');
    }
}