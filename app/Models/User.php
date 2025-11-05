<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password_hash',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'id_nivel_autorizacion',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_creacion' => 'datetime',
            'fecha_ultima_modificacion' => 'datetime',
            'ultimo_login' => 'datetime',
            'activo' => 'boolean',
        ];
    }

    /**
     * Get the password attribute name for authentication.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Relación con nivel de autorización
     */
    public function nivelAutorizacion()
    {
        return $this->belongsTo(NivelAutorizacion::class, 'id_nivel_autorizacion', 'id_nivel');
    }

    /**
     * Relación con encargados de tienda
     */
    public function encargadosTienda()
    {
        return $this->hasMany(EncargadoTienda::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación con notas creadas
     */
    public function notasCreadas()
    {
        return $this->hasMany(NotaMovimiento::class, 'id_usuario_crea', 'id_usuario');
    }

    /**
     * Relación con notas enviadas
     */
    public function notasEnviadas()
    {
        return $this->hasMany(NotaMovimiento::class, 'id_usuario_envia', 'id_usuario');
    }

    /**
     * Relación con notas recibidas
     */
    public function notasRecibidas()
    {
        return $this->hasMany(NotaMovimiento::class, 'id_usuario_recibe', 'id_usuario');
    }
}
