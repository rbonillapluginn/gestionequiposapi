<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadEnvio extends Model
{
    use HasFactory;

    protected $table = 'unidades_envio';
    protected $primaryKey = 'id_unidad_envio';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_unidad',
        'id_tipo_material',
        'id_color',
        'tiene_cintillo',
        'dimensiones',
        'peso_maximo',
        'descripcion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'tiene_cintillo' => 'boolean',
            'peso_maximo' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    protected $appends = ['descripcion_completa'];

    /**
     * Accessor para obtener descripción completa de la unidad de envío
     */
    public function getDescripcionCompletaAttribute()
    {
        $partes = [];

        // Tipo de unidad
        if ($this->tipoUnidad) {
            $partes[] = $this->tipoUnidad->nombre_tipo;
        }

        // Tipo de material
        if ($this->tipoMaterial) {
            $partes[] = $this->tipoMaterial->nombre_material;
        }

        // Color
        if ($this->color) {
            $partes[] = "Color: " . $this->color->nombre_color;
        }

        // Cintillo
        if ($this->tiene_cintillo) {
            $partes[] = "Con cintillo";
        }

        // Dimensiones
        if ($this->dimensiones) {
            $partes[] = "Dim: " . $this->dimensiones;
        }

        // Peso máximo
        if ($this->peso_maximo) {
            $partes[] = "Peso: " . $this->peso_maximo . " kg";
        }

        // Descripción adicional
        if ($this->descripcion) {
            $partes[] = $this->descripcion;
        }

        return !empty($partes) ? implode(' | ', $partes) : 'Sin especificar';
    }

    /**
     * Relación con tipo de unidad
     */
    public function tipoUnidad()
    {
        return $this->belongsTo(TipoUnidadEnvio::class, 'id_tipo_unidad', 'id_tipo_unidad');
    }

    /**
     * Relación con tipo de material
     */
    public function tipoMaterial()
    {
        return $this->belongsTo(TipoMaterial::class, 'id_tipo_material', 'id_tipo_material');
    }

    /**
     * Relación con color
     */
    public function color()
    {
        return $this->belongsTo(Color::class, 'id_color', 'id_color');
    }

    /**
     * Relación con detalles de nota
     */
    public function detallesNota()
    {
        return $this->hasMany(DetalleNotaArticulo::class, 'id_unidad_envio', 'id_unidad_envio');
    }
}