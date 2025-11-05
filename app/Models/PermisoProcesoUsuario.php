<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoProcesoUsuario extends Model
{
    use HasFactory;

    protected $table = 'permisos_procesos_usuario';
    protected $primaryKey = 'id_permiso_proceso';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_estado',
        'tiene_permiso',
        'id_usuario_asigna',
    ];

    protected function casts(): array
    {
        return [
            'tiene_permiso' => 'boolean',
            'fecha_asignacion' => 'datetime',
        ];
    }

    /**
     * Relación con usuario que tiene el permiso
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación con el estado/proceso
     */
    public function estado()
    {
        return $this->belongsTo(EstadoNota::class, 'id_estado', 'id_estado');
    }

    /**
     * Relación con usuario que asignó el permiso
     */
    public function usuarioAsigna()
    {
        return $this->belongsTo(User::class, 'id_usuario_asigna', 'id_usuario');
    }

    /**
     * Scope para filtrar solo permisos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('tiene_permiso', true);
    }

    /**
     * Scope para filtrar por usuario
     */
    public function scopePorUsuario($query, $idUsuario)
    {
        return $query->where('id_usuario', $idUsuario);
    }

    /**
     * Scope para filtrar por estado/proceso
     */
    public function scopePorEstado($query, $idEstado)
    {
        return $query->where('id_estado', $idEstado);
    }

    /**
     * Verificar si un usuario tiene permiso para un proceso
     */
    public static function tienePermiso($idUsuario, $idEstado)
    {
        $permiso = self::where('id_usuario', $idUsuario)
            ->where('id_estado', $idEstado)
            ->first();
        
        return $permiso ? $permiso->tiene_permiso : false;
    }
}
