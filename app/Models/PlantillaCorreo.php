<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaCorreo extends Model
{
    use HasFactory;

    protected $table = 'plantillas_correo';
    protected $primaryKey = 'id_plantilla';
    public $timestamps = false;

    protected $fillable = [
        'nombre_plantilla',
        'asunto',
        'cuerpo_html',
        'cuerpo_texto',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación con log de correos
     */
    public function logCorreos()
    {
        return $this->hasMany(LogCorreo::class, 'id_plantilla', 'id_plantilla');
    }
}