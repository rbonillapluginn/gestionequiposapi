<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotaMovimiento;
use App\Models\DetalleNotaArticulo;
use App\Models\HistorialEstadoNota;
use App\Models\EstadoNota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\NotificationService;

class NotaMovimientoController extends Controller
{
    /**
     * Listar notas de movimiento
     */
    public function index(Request $request)
    {
        $query = NotaMovimiento::with([
            'tipoMovimiento',
            'tiendaOrigen',
            'tiendaDestino',
            'proveedorDestino',
            'metodoEnvio',
            'submetodoEnvio',
            'vehiculo',
            'chofer',
            'mensajero',
            'usuarioCrea',
            'usuarioEnvia',
            'usuarioRecibe',
            'estado',
            'detallesArticulos.articulo',
            'detallesArticulos.unidadEnvio'
        ]);

        // Filtros
        if ($request->filled('tipo_nota')) {
            $query->where('tipo_nota', $request->tipo_nota);
        }

        if ($request->filled('estado')) {
            $query->where('id_estado', $request->estado);
        }

        if ($request->filled('id_estado')) {
            $query->where('id_estado', $request->id_estado);
        }

        if ($request->filled('id_tienda_origen')) {
            $query->where('id_tienda_origen', $request->id_tienda_origen);
        }

        if ($request->filled('id_tienda_destino')) {
            $query->where('id_tienda_destino', $request->id_tienda_destino);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_creacion', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_creacion', '<=', $request->fecha_hasta);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_creacion', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_creacion', '<=', $request->fecha_fin);
        }

        if ($request->filled('search')) {
            $query->where('numero_nota', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('numero_nota')) {
            $query->where('numero_nota', 'LIKE', "%{$request->numero_nota}%");
        }

        $query->orderBy('fecha_creacion', 'desc');

        $perPage = $request->get('per_page', 15);
        $notas = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $notas
        ]);
    }

    /**
     * Crear nota de movimiento
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_nota' => 'required|in:ENTRADA,SALIDA',
            'id_tipo_movimiento' => 'required|exists:tipos_movimiento,id_tipo_movimiento',
            'id_tienda_origen' => 'nullable|exists:tiendas,id_tienda',
            'id_tienda_destino' => 'nullable|exists:tiendas,id_tienda',
            'id_proveedor_destino' => 'nullable|exists:proveedores,id_proveedor',
            'id_metodo_envio' => 'required|exists:metodos_envio,id_metodo_envio',
            'id_submetodo_envio' => 'nullable|exists:submetodos_envio,id_submetodo',
            'id_vehiculo' => 'nullable|exists:vehiculos,id_vehiculo',
            'id_chofer' => 'nullable|exists:choferes,id_chofer',
            'hora_salida' => 'nullable|date',
            'id_mensajero' => 'nullable|exists:mensajeros,id_mensajero',
            'observaciones' => 'nullable|string',
            'articulos' => 'required|array|min:1',
            'articulos.*.id_articulo' => 'required|exists:articulos,id_articulo',
            'articulos.*.cantidad' => 'required|integer|min:1',
            // Campos para crear unidad de envío dinámicamente
            'articulos.*.unidad_envio.id_tipo_unidad' => 'required|exists:tipos_unidad_envio,id_tipo_unidad',
            'articulos.*.unidad_envio.id_tipo_material' => 'nullable|exists:tipos_material,id_tipo_material',
            'articulos.*.unidad_envio.id_color' => 'nullable|exists:colores,id_color',
            'articulos.*.unidad_envio.tiene_cintillo' => 'boolean',
            'articulos.*.unidad_envio.dimensiones' => 'nullable|string|max:100',
            'articulos.*.unidad_envio.peso_maximo' => 'nullable|numeric',
            'articulos.*.unidad_envio.descripcion' => 'nullable|string',
            'articulos.*.observaciones' => 'nullable|string',
        ]);

        // Validación personalizada para origen y destino
        $validator->after(function ($validator) use ($request) {
            $tieneOrigen = $request->id_tienda_origen;
            $tieneDestino = $request->id_tienda_destino || $request->id_proveedor_destino;

            if (!$tieneOrigen) {
                $validator->errors()->add('origen', 'Debe especificar una tienda de origen');
            }

            if (!$tieneDestino) {
                $validator->errors()->add('destino', 'Debe especificar una tienda de destino o proveedor de destino');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Generar número de nota único
            $numeroNota = $this->generarNumeroNota($request->tipo_nota);

            // Estado inicial
            $estadoInicial = EstadoNota::where('nombre_estado', 'CREADA')->first();

            // Crear nota de movimiento
            $nota = NotaMovimiento::create([
                'numero_nota' => $numeroNota,
                'tipo_nota' => $request->tipo_nota,
                'id_tipo_movimiento' => $request->id_tipo_movimiento,
                'id_tienda_origen' => $request->id_tienda_origen,
                'id_tienda_destino' => $request->id_tienda_destino,
                'id_proveedor_destino' => $request->id_proveedor_destino,
                'id_metodo_envio' => $request->id_metodo_envio,
                'id_submetodo_envio' => $request->id_submetodo_envio,
                'id_vehiculo' => $request->id_vehiculo,
                'id_chofer' => $request->id_chofer,
                'hora_salida' => $request->hora_salida,
                'id_mensajero' => $request->id_mensajero,
                'id_usuario_crea' => $request->user()->id_usuario,
                'id_estado' => $estadoInicial->id_estado,
                'observaciones' => $request->observaciones,
            ]);

            // Crear detalles de artículos
            foreach ($request->articulos as $articulo) {
                $idUnidadEnvio = null;
                
                // Crear unidad de envío dinámicamente si se especificaron los datos
                if (isset($articulo['unidad_envio'])) {
                    $unidadEnvioData = $articulo['unidad_envio'];
                    
                    // Crear la unidad de envío
                    $unidadEnvio = \App\Models\UnidadEnvio::create([
                        'id_tipo_unidad' => $unidadEnvioData['id_tipo_unidad'],
                        'id_tipo_material' => $unidadEnvioData['id_tipo_material'] ?? null,
                        'id_color' => $unidadEnvioData['id_color'] ?? null,
                        'tiene_cintillo' => $unidadEnvioData['tiene_cintillo'] ?? false,
                        'dimensiones' => $unidadEnvioData['dimensiones'] ?? null,
                        'peso_maximo' => $unidadEnvioData['peso_maximo'] ?? null,
                        'descripcion' => $unidadEnvioData['descripcion'] ?? null,
                        'activo' => true,
                    ]);
                    
                    $idUnidadEnvio = $unidadEnvio->id_unidad_envio;
                }
                
                // Crear detalle de artículo
                DetalleNotaArticulo::create([
                    'id_nota' => $nota->id_nota,
                    'id_articulo' => $articulo['id_articulo'],
                    'cantidad' => $articulo['cantidad'],
                    'id_unidad_envio' => $idUnidadEnvio,
                    'observaciones' => $articulo['observaciones'] ?? null,
                ]);
            }

            // Crear historial de estado inicial
            HistorialEstadoNota::create([
                'id_nota' => $nota->id_nota,
                'id_estado_anterior' => null,
                'id_estado_nuevo' => $estadoInicial->id_estado,
                'id_usuario' => $request->user()->id_usuario,
                'observaciones' => 'Nota creada',
            ]);

            DB::commit();

            // Refrescar la nota para obtener los campos con valores por defecto de la BD
            $nota->refresh();

            // Enviar notificación por correo
            $notificationService = new NotificationService();
            $notificationService->notificarNotaCreada($nota);

            return response()->json([
                'success' => true,
                'message' => 'Nota de movimiento creada exitosamente',
                'data' => $nota->load([
                    'tipoMovimiento',
                    'tiendaOrigen',
                    'tiendaDestino',
                    'proveedorDestino',
                    'metodoEnvio',
                    'submetodoEnvio',
                    'vehiculo',
                    'chofer',
                    'mensajero',
                    'usuarioCrea',
                    'estado',
                    'detallesArticulos.articulo',
                    'detallesArticulos.unidadEnvio'
                ])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la nota de movimiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar nota específica
     */
    public function show($id)
    {
        $nota = NotaMovimiento::with([
            'tipoMovimiento',
            'tiendaOrigen',
            'tiendaDestino',
            'proveedorDestino',
            'metodoEnvio',
            'submetodoEnvio',
            'vehiculo',
            'chofer',
            'mensajero',
            'usuarioCrea',
            'usuarioEnvia',
            'usuarioRecibe',
            'estado',
            'detallesArticulos.articulo.categoria',
            'detallesArticulos.unidadEnvio.tipoUnidad',
            'detallesArticulos.unidadEnvio.tipoMaterial',
            'detallesArticulos.unidadEnvio.color',
            'historialEstados.estadoAnterior',
            'historialEstados.estadoNuevo',
            'historialEstados.usuario'
        ])->find($id);

        if (!$nota) {
            return response()->json([
                'success' => false,
                'message' => 'Nota no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $nota
        ]);
    }

    /**
     * Actualizar nota de movimiento
     */
    public function update(Request $request, $id)
    {
        $nota = NotaMovimiento::find($id);

        if (!$nota) {
            return response()->json([
                'success' => false,
                'message' => 'Nota no encontrada'
            ], 404);
        }

        // Validar que la nota esté en estado CREADA para poder editarla
        if ($nota->id_estado != 1) { // 1 = CREADA
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden editar notas en estado CREADA'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'tipo_nota' => 'sometimes|in:ENTRADA,SALIDA',
            'id_tipo_movimiento' => 'sometimes|exists:tipos_movimiento,id_tipo_movimiento',
            'id_tienda_origen' => 'nullable|exists:tiendas,id_tienda',
            'id_tienda_destino' => 'nullable|exists:tiendas,id_tienda',
            'id_proveedor_destino' => 'nullable|exists:proveedores,id_proveedor',
            'id_metodo_envio' => 'sometimes|exists:metodos_envio,id_metodo_envio',
            'id_submetodo_envio' => 'nullable|exists:submetodos_envio,id_submetodo',
            'id_vehiculo' => 'nullable|exists:vehiculos,id_vehiculo',
            'id_chofer' => 'nullable|exists:choferes,id_chofer',
            'hora_salida' => 'nullable|date',
            'id_mensajero' => 'nullable|exists:mensajeros,id_mensajero',
            'observaciones' => 'nullable|string',
            'articulos' => 'sometimes|array|min:1',
            'articulos.*.id_detalle' => 'nullable|exists:detalles_nota_articulo,id_detalle',
            'articulos.*.id_articulo' => 'required|exists:articulos,id_articulo',
            'articulos.*.cantidad' => 'required|integer|min:1',
            'articulos.*.unidad_envio.id_tipo_unidad' => 'required|exists:tipos_unidad_envio,id_tipo_unidad',
            'articulos.*.unidad_envio.id_tipo_material' => 'nullable|exists:tipos_material,id_tipo_material',
            'articulos.*.unidad_envio.id_color' => 'nullable|exists:colores,id_color',
            'articulos.*.unidad_envio.tiene_cintillo' => 'boolean',
            'articulos.*.unidad_envio.dimensiones' => 'nullable|string|max:100',
            'articulos.*.unidad_envio.peso_maximo' => 'nullable|numeric',
            'articulos.*.unidad_envio.descripcion' => 'nullable|string',
            'articulos.*.observaciones' => 'nullable|string',
        ]);

        // Validación personalizada para origen y destino
        $validator->after(function ($validator) use ($request, $nota) {
            $tieneOrigen = $request->has('id_tienda_origen') ? $request->id_tienda_origen : $nota->id_tienda_origen;
            $tieneDestino = ($request->has('id_tienda_destino') ? $request->id_tienda_destino : $nota->id_tienda_destino) 
                            || ($request->has('id_proveedor_destino') ? $request->id_proveedor_destino : $nota->id_proveedor_destino);

            if (!$tieneOrigen) {
                $validator->errors()->add('origen', 'Debe especificar una tienda de origen');
            }

            if (!$tieneDestino) {
                $validator->errors()->add('destino', 'Debe especificar una tienda de destino o proveedor de destino');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Actualizar datos principales de la nota
            $nota->update($request->only([
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
                'observaciones',
            ]));

            // Actualizar artículos si se enviaron
            if ($request->has('articulos')) {
                // Obtener IDs de detalles actuales
                $detallesActuales = $nota->detallesArticulos->pluck('id_detalle')->toArray();
                $detallesEnviados = [];

                foreach ($request->articulos as $articulo) {
                    if (isset($articulo['id_detalle'])) {
                        // Actualizar detalle existente
                        $detalle = DetalleNotaArticulo::find($articulo['id_detalle']);
                        
                        if ($detalle && $detalle->id_nota == $nota->id_nota) {
                            $detalle->update([
                                'id_articulo' => $articulo['id_articulo'],
                                'cantidad' => $articulo['cantidad'],
                                'observaciones' => $articulo['observaciones'] ?? null,
                            ]);

                            // Actualizar unidad de envío si existe
                            if (isset($articulo['unidad_envio']) && $detalle->id_unidad_envio) {
                                $unidadEnvio = \App\Models\UnidadEnvio::find($detalle->id_unidad_envio);
                                if ($unidadEnvio) {
                                    $unidadEnvio->update([
                                        'id_tipo_unidad' => $articulo['unidad_envio']['id_tipo_unidad'],
                                        'id_tipo_material' => $articulo['unidad_envio']['id_tipo_material'] ?? null,
                                        'id_color' => $articulo['unidad_envio']['id_color'] ?? null,
                                        'tiene_cintillo' => $articulo['unidad_envio']['tiene_cintillo'] ?? false,
                                        'dimensiones' => $articulo['unidad_envio']['dimensiones'] ?? null,
                                        'peso_maximo' => $articulo['unidad_envio']['peso_maximo'] ?? null,
                                        'descripcion' => $articulo['unidad_envio']['descripcion'] ?? null,
                                    ]);
                                }
                            }

                            $detallesEnviados[] = $articulo['id_detalle'];
                        }
                    } else {
                        // Crear nuevo detalle
                        $idUnidadEnvio = null;
                        
                        if (isset($articulo['unidad_envio'])) {
                            $unidadEnvioData = $articulo['unidad_envio'];
                            
                            $unidadEnvio = \App\Models\UnidadEnvio::create([
                                'id_tipo_unidad' => $unidadEnvioData['id_tipo_unidad'],
                                'id_tipo_material' => $unidadEnvioData['id_tipo_material'] ?? null,
                                'id_color' => $unidadEnvioData['id_color'] ?? null,
                                'tiene_cintillo' => $unidadEnvioData['tiene_cintillo'] ?? false,
                                'dimensiones' => $unidadEnvioData['dimensiones'] ?? null,
                                'peso_maximo' => $unidadEnvioData['peso_maximo'] ?? null,
                                'descripcion' => $unidadEnvioData['descripcion'] ?? null,
                                'activo' => true,
                            ]);
                            
                            $idUnidadEnvio = $unidadEnvio->id_unidad_envio;
                        }
                        
                        $nuevoDetalle = DetalleNotaArticulo::create([
                            'id_nota' => $nota->id_nota,
                            'id_articulo' => $articulo['id_articulo'],
                            'cantidad' => $articulo['cantidad'],
                            'id_unidad_envio' => $idUnidadEnvio,
                            'observaciones' => $articulo['observaciones'] ?? null,
                        ]);

                        $detallesEnviados[] = $nuevoDetalle->id_detalle;
                    }
                }

                // Eliminar detalles que ya no están en la lista
                $detallesEliminar = array_diff($detallesActuales, $detallesEnviados);
                if (!empty($detallesEliminar)) {
                    DetalleNotaArticulo::whereIn('id_detalle', $detallesEliminar)->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Nota actualizada exitosamente',
                'data' => $nota->load([
                    'tipoMovimiento',
                    'tiendaOrigen',
                    'tiendaDestino',
                    'proveedorDestino',
                    'metodoEnvio',
                    'submetodoEnvio',
                    'vehiculo',
                    'chofer',
                    'mensajero',
                    'usuarioCrea',
                    'estado',
                    'detallesArticulos.articulo',
                    'detallesArticulos.unidadEnvio'
                ])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la nota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar estado de nota
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_estado' => 'required|exists:estados_nota,id_estado',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $nota = NotaMovimiento::find($id);

        if (!$nota) {
            return response()->json([
                'success' => false,
                'message' => 'Nota no encontrada'
            ], 404);
        }

        // NUEVA VALIDACIÓN: Verificar permiso de proceso
        $tienePermiso = \App\Models\PermisoProcesoUsuario::tienePermiso(
            $request->user()->id_usuario,
            $request->id_estado
        );

        if (!$tienePermiso) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permiso para cambiar la nota a este estado'
            ], 403);
        }

        DB::beginTransaction();

        try {
            $estadoAnterior = $nota->id_estado;
            $estadoNuevo = $request->id_estado;

            // Si el estado es "EN_TRANSITO", registrar fecha de envío y usuario
            if ($request->id_estado == 2) { // EN_TRANSITO
                $nota->update([
                    'fecha_envio' => now(),
                    'id_usuario_envia' => $request->user()->id_usuario,
                    'id_estado' => $estadoNuevo
                ]);
            }
            // Si el estado es "RECIBIDA", registrar fecha de recepción y usuario
            elseif ($request->id_estado == 3) { // RECIBIDA
                $nota->update([
                    'fecha_recepcion' => now(),
                    'id_usuario_recibe' => $request->user()->id_usuario,
                    'id_estado' => $estadoNuevo
                ]);
            }
            else {
                $nota->update(['id_estado' => $estadoNuevo]);
            }

            // Crear historial de cambio de estado
            HistorialEstadoNota::create([
                'id_nota' => $nota->id_nota,
                'id_estado_anterior' => $estadoAnterior,
                'id_estado_nuevo' => $estadoNuevo,
                'id_usuario' => $request->user()->id_usuario,
                'observaciones' => $request->observaciones,
            ]);

            DB::commit();

            // Enviar notificación por correo según el estado
            $notificationService = new NotificationService();
            if ($estadoNuevo == 2) { // EN_TRANSITO
                $notificationService->notificarNotaEnviada($nota);
            } elseif ($estadoNuevo == 3) { // RECIBIDA
                $notificationService->notificarNotaRecibida($nota);
            }

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'data' => $nota->load('estado')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de estados de una nota
     */
    public function getHistorial($id)
    {
        $nota = NotaMovimiento::find($id);

        if (!$nota) {
            return response()->json([
                'success' => false,
                'message' => 'Nota no encontrada'
            ], 404);
        }

        $historial = HistorialEstadoNota::with([
            'estadoAnterior',
            'estadoNuevo',
            'usuario'
        ])
        ->where('id_nota', $id)
        ->orderBy('fecha_cambio', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $historial
        ]);
    }

    /**
     * Generar número de nota único
     */
    private function generarNumeroNota($tipoNota)
    {
        $prefijo = $tipoNota === 'ENTRADA' ? 'ENT' : 'SAL';
        $fecha = date('Ymd');
        
        // Buscar el último número del día
        $ultimaNota = NotaMovimiento::where('numero_nota', 'LIKE', "{$prefijo}-{$fecha}%")
            ->orderBy('numero_nota', 'desc')
            ->first();

        if ($ultimaNota) {
            $ultimoNumero = intval(substr($ultimaNota->numero_nota, -4));
            $nuevoNumero = str_pad($ultimoNumero + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nuevoNumero = '0001';
        }

        return "{$prefijo}-{$fecha}-{$nuevoNumero}";
    }

    /**
     * Eliminar nota de movimiento (solo si está en estado CREADA)
     */
    public function destroy($id)
    {
        $nota = NotaMovimiento::find($id);

        if (!$nota) {
            return response()->json([
                'success' => false,
                'message' => 'Nota no encontrada'
            ], 404);
        }

        // Validar que la nota esté en estado CREADA para poder eliminarla
        if ($nota->id_estado != 1) { // 1 = CREADA
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden eliminar notas en estado CREADA'
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Eliminar detalles de artículos
            DetalleNotaArticulo::where('id_nota', $nota->id_nota)->delete();

            // Eliminar historial de estados
            HistorialEstadoNota::where('id_nota', $nota->id_nota)->delete();

            // Eliminar logs de correos
            \App\Models\LogCorreo::where('id_nota', $nota->id_nota)->delete();

            // Eliminar firmas digitales (si existen)
            \App\Models\FirmaDigital::where('id_nota', $nota->id_nota)->delete();

            // Eliminar la nota
            $nota->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Nota eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la nota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dashboard/Monitor de envíos y recepciones
     */
    public function dashboard(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());

        // Estadísticas generales
        $stats = [
            'total_notas' => NotaMovimiento::whereBetween('fecha_creacion', [$fechaInicio, $fechaFin])->count(),
            'notas_pendientes' => NotaMovimiento::where('id_estado', 1)->count(), // CREADA
            'notas_en_transito' => NotaMovimiento::where('id_estado', 2)->count(), // EN_TRANSITO
            'notas_recibidas' => NotaMovimiento::where('id_estado', 3)->count(), // RECIBIDA
            'notas_canceladas' => NotaMovimiento::where('id_estado', 4)->count(), // CANCELADA
        ];

        // Notas por estado
        $notasPorEstado = NotaMovimiento::select('id_estado', DB::raw('count(*) as total'))
            ->with('estado')
            ->whereBetween('fecha_creacion', [$fechaInicio, $fechaFin])
            ->groupBy('id_estado')
            ->get();

        // Notas recientes
        $notasRecientes = NotaMovimiento::with([
            'tiendaOrigen',
            'tiendaDestino',
            'estado',
            'usuarioCrea'
        ])
        ->orderBy('fecha_creacion', 'desc')
        ->limit(10)
        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'estadisticas' => $stats,
                'notas_por_estado' => $notasPorEstado,
                'notas_recientes' => $notasRecientes
            ]
        ]);
    }
}