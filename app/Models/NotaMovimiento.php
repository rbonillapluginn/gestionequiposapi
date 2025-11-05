<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaMovimiento extends Model
{
    use HasFactory;

    protected $table = 'notas_movimiento';
    protected $primaryKey = 'id_nota';
    public $timestamps = false;

    protected $fillable = [
        'numero_nota',
        'tipo_nota',
        'id_tipo_movimiento',
        'id_tienda_origen',
        'id_tienda_destino',
        'id_proveedor_destino',
        'id_metodo_envio',
        'id_submetodo_envio',
        'id_vehiculo',
        'id_chofer',
        'hora_salida',
        'id_mensajero',
        'fecha_envio',
        'fecha_recepcion',
        'id_usuario_crea',
        'id_usuario_envia',
        'id_usuario_recibe',
        'id_estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_creacion' => 'datetime',
            'fecha_envio' => 'datetime',
            'fecha_recepcion' => 'datetime',
            'hora_salida' => 'datetime',
        ];
    }

    /**
     * Relación con tipo de movimiento
     */
    public function tipoMovimiento()
    {
        return $this->belongsTo(TipoMovimiento::class, 'id_tipo_movimiento', 'id_tipo_movimiento');
    }

    /**
     * Relación con tienda origen
     */
    public function tiendaOrigen()
    {
        return $this->belongsTo(Tienda::class, 'id_tienda_origen', 'id_tienda');
    }

    /**
     * Relación con tienda destino
     */
    public function tiendaDestino()
    {
        return $this->belongsTo(Tienda::class, 'id_tienda_destino', 'id_tienda');
    }

    /**
     * Relación con proveedor destino
     */
    public function proveedorDestino()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor_destino', 'id_proveedor');
    }

    /**
     * Relación con método de envío
     */
    public function metodoEnvio()
    {
        return $this->belongsTo(MetodoEnvio::class, 'id_metodo_envio', 'id_metodo_envio');
    }

    /**
     * Relación con submétodo de envío
     */
    public function submetodoEnvio()
    {
        return $this->belongsTo(SubmetodoEnvio::class, 'id_submetodo_envio', 'id_submetodo');
    }

    /**
     * Relación con vehículo
     */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo', 'id_vehiculo');
    }

    /**
     * Relación con chofer
     */
    public function chofer()
    {
        return $this->belongsTo(Chofer::class, 'id_chofer', 'id_chofer');
    }

    /**
     * Relación con mensajero
     */
    public function mensajero()
    {
        return $this->belongsTo(Mensajero::class, 'id_mensajero', 'id_mensajero');
    }

    /**
     * Relación con usuario creador
     */
    public function usuarioCrea()
    {
        return $this->belongsTo(User::class, 'id_usuario_crea', 'id_usuario');
    }

    /**
     * Relación con usuario que envía
     */
    public function usuarioEnvia()
    {
        return $this->belongsTo(User::class, 'id_usuario_envia', 'id_usuario');
    }

    /**
     * Relación con usuario que recibe
     */
    public function usuarioRecibe()
    {
        return $this->belongsTo(User::class, 'id_usuario_recibe', 'id_usuario');
    }

    /**
     * Relación con estado
     */
    public function estado()
    {
        return $this->belongsTo(EstadoNota::class, 'id_estado', 'id_estado');
    }

    /**
     * Relación con detalles de artículos
     */
    public function detallesArticulos()
    {
        return $this->hasMany(DetalleNotaArticulo::class, 'id_nota', 'id_nota');
    }

    /**
     * Relación con log de correos
     */
    public function logCorreos()
    {
        return $this->hasMany(LogCorreo::class, 'id_nota', 'id_nota');
    }

    /**
     * Relación con historial de estados
     */
    public function historialEstados()
    {
        return $this->hasMany(HistorialEstadoNota::class, 'id_nota', 'id_nota');
    }
}