<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $table = 'articulos';
    protected $primaryKey = 'id_articulo';
    public $timestamps = false;

    protected $fillable = [
        'nombre_articulo',
        'descripcion',
        'id_categoria',
        'codigo_barra',
        'numero_serie',
        'marca',
        'modelo',
        'estado',
        'observaciones',
        'precio',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_creacion' => 'datetime',
            'fecha_ultima_modificacion' => 'datetime',
            'precio' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación con categoría
     */
    public function categoria()
    {
        return $this->belongsTo(CategoriaArticulo::class, 'id_categoria', 'id_categoria');
    }

    /**
     * Relación con detalles de nota
     */
    public function detallesNota()
    {
        return $this->hasMany(DetalleNotaArticulo::class, 'id_articulo', 'id_articulo');
    }
}