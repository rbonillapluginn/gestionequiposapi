<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotaMovimiento;
use App\Models\FirmaDigital;
use App\Models\HistorialEstadoNota;
use App\Models\EstadoNota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\NotificationService;

class FirmaDigitalController extends Controller
{
    /**
     * Registrar firma digital y cambiar estado de nota
     */
    public function firmarYCambiarEstado(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'firmante.nombre_completo' => 'required|string|max:255',
            'firmante.cedula' => 'required|string|max:50',
            'firmante.cargo' => 'nullable|string|max:100',
            'firma_base64' => 'required|string',
            'nuevo_estado' => 'required',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $nota = NotaMovimiento::with([
                'tiendaOrigen',
                'tiendaDestino',
                'proveedorDestino',
                'detallesArticulos.articulo',
                'detallesArticulos.unidadEnvio.tipoUnidad',
                'metodoEnvio',
                'estado'
            ])->findOrFail($id);

            $estadoAnterior = $nota->id_estado;
            
            // Convertir nombre de estado a ID si viene como string
            $nuevoEstadoInput = $request->input('nuevo_estado');
            if (is_numeric($nuevoEstadoInput)) {
                $estadoNuevo = (int)$nuevoEstadoInput;
                $estadoNuevoObj = EstadoNota::find($estadoNuevo);
            } else {
                // Buscar por nombre (ej: "en_transito" -> "EN_TRANSITO")
                $nombreEstadoBuscar = strtoupper(str_replace('_', ' ', $nuevoEstadoInput));
                $estadoNuevoObj = EstadoNota::where('nombre_estado', $nombreEstadoBuscar)->first();
                
                if (!$estadoNuevoObj) {
                    // Intentar con guión bajo
                    $nombreEstadoBuscar = strtoupper($nuevoEstadoInput);
                    $estadoNuevoObj = EstadoNota::where('nombre_estado', $nombreEstadoBuscar)->first();
                }
                
                if (!$estadoNuevoObj) {
                    return response()->json([
                        'success' => false,
                        'message' => "Estado '{$nuevoEstadoInput}' no encontrado"
                    ], 422);
                }
                
                $estadoNuevo = $estadoNuevoObj->id_estado;
            }
            
            if (!$estadoNuevoObj) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estado no válido'
                ], 422);
            }
            
            $estadoAnteriorNombre = $nota->estado ? $nota->estado->nombre_estado : '';
            $estadoNuevoNombre = $estadoNuevoObj->nombre_estado;

            // Determinar tipo de firma según el estado nuevo
            $tipoFirma = 'despacho'; // Por defecto
            if ($estadoNuevo == 2) { // EN_TRANSITO
                $tipoFirma = 'despacho'; // Firma de quien despacha/envía
            } elseif ($estadoNuevo == 3) { // RECIBIDA
                $tipoFirma = 'recepcion'; // Firma de quien recibe
            }

            // Registrar firma digital
            $firma = FirmaDigital::create([
                'id_nota' => $id,
                'nombre_completo_firmante' => $request->input('firmante.nombre_completo'),
                'cedula_firmante' => $request->input('firmante.cedula'),
                'cargo_firmante' => $request->input('firmante.cargo'),
                'firma_base64' => $request->input('firma_base64'),
                'tipo_firma' => $tipoFirma,
                'estado_anterior' => $estadoAnteriorNombre,
                'estado_nuevo' => $estadoNuevoNombre,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'fecha_firma' => now(),
            ]);

            // Cambiar estado de la nota
            if ($estadoNuevo == 2) { // EN_TRANSITO
                $nota->update([
                    'id_estado' => $estadoNuevo,
                    'fecha_envio' => now(),
                    'id_usuario_envia' => $request->user()->id_usuario,
                ]);
            } elseif ($estadoNuevo == 3) { // RECIBIDA
                $nota->update([
                    'id_estado' => $estadoNuevo,
                    'fecha_recepcion' => now(),
                    'id_usuario_recibe' => $request->user()->id_usuario,
                ]);
            } else {
                $nota->update([
                    'id_estado' => $estadoNuevo,
                ]);
            }

            // Registrar en historial de estados
            HistorialEstadoNota::create([
                'id_nota' => $id,
                'id_estado_anterior' => $estadoAnterior,
                'id_estado_nuevo' => $estadoNuevo,
                'id_usuario' => $request->user()->id_usuario,
                'observaciones' => $request->input('observaciones') ?? 'Cambio de estado con firma digital',
            ]);

            // Enviar notificación por correo según el estado
            $notificationService = new NotificationService();
            if ($estadoNuevo == 2) { // EN_TRANSITO
                $notificationService->notificarNotaEnviada($nota);
            } elseif ($estadoNuevo == 3) { // RECIBIDA
                $notificationService->notificarNotaRecibida($nota);
            }

            DB::commit();

            // Recargar nota con todas las relaciones
            $nota->load([
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

            return response()->json([
                'success' => true,
                'message' => 'Firma registrada y estado actualizado exitosamente',
                'data' => [
                    'id_firma' => $firma->id_firma,
                    'nota' => $nota,
                    'firma' => $firma,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar firma: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener firmas de una nota
     */
    public function getFirmasPorNota($id)
    {
        try {
            $firmas = FirmaDigital::where('id_nota', $id)
                ->orderBy('fecha_firma', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $firmas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener firmas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalle de una firma específica
     */
    public function show($id)
    {
        try {
            $firma = FirmaDigital::with('nota')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $firma
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Firma no encontrada'
            ], 404);
        }
    }
}
