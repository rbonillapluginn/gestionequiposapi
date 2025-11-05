<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncargadoTienda extends Model
{
    use HasFactory;

    protected $table = 'encargados_tienda';
    protected $primaryKey = 'id_encargado';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_tienda',
        'id_departamento',
        'es_principal',
        'fecha_asignacion',
        'fecha_fin',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'date',
            'fecha_fin' => 'date',
            'es_principal' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación con usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación con tienda
     */
    public function tienda()
    {
        return $this->belongsTo(Tienda::class, 'id_tienda', 'id_tienda');
    }

    /**
     * Relación con departamento
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento', 'id_departamento');
    }
}