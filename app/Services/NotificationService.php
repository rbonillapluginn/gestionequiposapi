<?php

namespace App\Services;

use App\Models\NotaMovimiento;
use App\Models\PlantillaCorreo;
use App\Models\LogCorreo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Enviar correo cuando se crea una nota
     */
    public function notificarNotaCreada(NotaMovimiento $nota)
    {
        try {
            // Cargar relaciones necesarias
            $nota->load(['detallesArticulos.articulo', 'usuarioCrea', 'tiendaOrigen', 'tiendaDestino', 'proveedorDestino', 'metodoEnvio', 'estado']);
            
            $plantilla = PlantillaCorreo::where('nombre_plantilla', 'nota_creada')
                ->where('activo', true)
                ->first();

            if (!$plantilla) {
                Log::warning('Plantilla de correo "nota_creada" no encontrada');
                return false;
            }

            $destinatarios = $this->obtenerDestinatarios($nota, 'creada');
            
            if (empty($destinatarios)) {
                Log::info("No hay destinatarios para la nota {$nota->numero_nota}");
                return false;
            }

            $asunto = $this->procesarPlantilla($plantilla->asunto, $nota);
            $cuerpoHtml = $this->procesarPlantilla($plantilla->cuerpo_html, $nota);
            $cuerpoTexto = $this->procesarPlantilla($plantilla->cuerpo_texto ?? '', $nota);

            // Registrar intento de envío
            $logCorreo = LogCorreo::create([
                'id_nota' => $nota->id_nota,
                'id_plantilla' => $plantilla->id_plantilla,
                'destinatarios' => implode(',', $destinatarios),
                'asunto' => $asunto,
                'enviado' => false,
            ]);

            // Enviar correo
            Mail::html($cuerpoHtml, function ($message) use ($destinatarios, $asunto, $cuerpoTexto) {
                $message->to($destinatarios)
                        ->subject($asunto);
                
                if ($cuerpoTexto) {
                    $message->text($cuerpoTexto);
                }
            });

            // Actualizar log como enviado
            $logCorreo->update([
                'enviado' => true,
                'fecha_envio' => now(),
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Error enviando correo para nota {$nota->numero_nota}: " . $e->getMessage());
            
            if (isset($logCorreo)) {
                $logCorreo->update([
                    'error' => $e->getMessage()
                ]);
            }
            
            return false;
        }
    }

    /**
     * Enviar correo cuando se envía una nota
     */
    public function notificarNotaEnviada(NotaMovimiento $nota)
    {
        try {
            // Cargar relaciones necesarias
            $nota->load(['detallesArticulos.articulo', 'usuarioCrea', 'usuarioEnvia', 'tiendaOrigen', 'tiendaDestino', 'proveedorDestino', 'metodoEnvio', 'estado']);
            
            $plantilla = PlantillaCorreo::where('nombre_plantilla', 'nota_enviada')
                ->where('activo', true)
                ->first();

            if (!$plantilla) {
                Log::warning('Plantilla de correo "nota_enviada" no encontrada');
                return false;
            }

            $destinatarios = $this->obtenerDestinatarios($nota, 'enviada');
            
            if (empty($destinatarios)) {
                return false;
            }

            $asunto = $this->procesarPlantilla($plantilla->asunto, $nota);
            $cuerpoHtml = $this->procesarPlantilla($plantilla->cuerpo_html, $nota);
            $cuerpoTexto = $this->procesarPlantilla($plantilla->cuerpo_texto ?? '', $nota);

            $logCorreo = LogCorreo::create([
                'id_nota' => $nota->id_nota,
                'id_plantilla' => $plantilla->id_plantilla,
                'destinatarios' => implode(',', $destinatarios),
                'asunto' => $asunto,
                'enviado' => false,
            ]);

            Mail::html($cuerpoHtml, function ($message) use ($destinatarios, $asunto, $cuerpoTexto) {
                $message->to($destinatarios)
                        ->subject($asunto);
                
                if ($cuerpoTexto) {
                    $message->text($cuerpoTexto);
                }
            });

            $logCorreo->update([
                'enviado' => true,
                'fecha_envio' => now(),
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Error enviando correo de nota enviada {$nota->numero_nota}: " . $e->getMessage());
            
            if (isset($logCorreo)) {
                $logCorreo->update([
                    'error' => $e->getMessage()
                ]);
            }
            
            return false;
        }
    }

    /**
     * Enviar correo cuando se recibe una nota
     */
    public function notificarNotaRecibida(NotaMovimiento $nota)
    {
        try {
            // Cargar relaciones necesarias
            $nota->load(['detallesArticulos.articulo', 'usuarioCrea', 'usuarioEnvia', 'usuarioRecibe', 'tiendaOrigen', 'tiendaDestino', 'proveedorDestino', 'metodoEnvio', 'estado']);
            
            $plantilla = PlantillaCorreo::where('nombre_plantilla', 'nota_recibida')
                ->where('activo', true)
                ->first();

            if (!$plantilla) {
                Log::warning('Plantilla de correo "nota_recibida" no encontrada');
                return false;
            }

            $destinatarios = $this->obtenerDestinatarios($nota, 'recibida');
            
            if (empty($destinatarios)) {
                return false;
            }

            $asunto = $this->procesarPlantilla($plantilla->asunto, $nota);
            $cuerpoHtml = $this->procesarPlantilla($plantilla->cuerpo_html, $nota);
            $cuerpoTexto = $this->procesarPlantilla($plantilla->cuerpo_texto ?? '', $nota);

            $logCorreo = LogCorreo::create([
                'id_nota' => $nota->id_nota,
                'id_plantilla' => $plantilla->id_plantilla,
                'destinatarios' => implode(',', $destinatarios),
                'asunto' => $asunto,
                'enviado' => false,
            ]);

            Mail::html($cuerpoHtml, function ($message) use ($destinatarios, $asunto, $cuerpoTexto) {
                $message->to($destinatarios)
                        ->subject($asunto);
                
                if ($cuerpoTexto) {
                    $message->text($cuerpoTexto);
                }
            });

            $logCorreo->update([
                'enviado' => true,
                'fecha_envio' => now(),
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Error enviando correo de nota recibida {$nota->numero_nota}: " . $e->getMessage());
            
            if (isset($logCorreo)) {
                $logCorreo->update([
                    'error' => $e->getMessage()
                ]);
            }
            
            return false;
        }
    }

    /**
     * Obtener destinatarios según el tipo de notificación
     */
    private function obtenerDestinatarios(NotaMovimiento $nota, string $tipoNotificacion): array
    {
        $destinatarios = [];

        switch ($tipoNotificacion) {
            case 'creada':
                // Notificar al creador
                if ($nota->usuarioCrea && $nota->usuarioCrea->email) {
                    $destinatarios[] = $nota->usuarioCrea->email;
                }
                
                // Notificar a encargados de tienda destino
                if ($nota->tiendaDestino) {
                    $encargados = $nota->tiendaDestino->encargados()
                        ->with('usuario')
                        ->where('activo', true)
                        ->get();
                    
                    foreach ($encargados as $encargado) {
                        if ($encargado->usuario && $encargado->usuario->email) {
                            $destinatarios[] = $encargado->usuario->email;
                        }
                    }
                }
                break;

            case 'enviada':
                // Notificar a encargados de tienda destino
                if ($nota->tiendaDestino) {
                    $encargados = $nota->tiendaDestino->encargados()
                        ->with('usuario')
                        ->where('activo', true)
                        ->get();
                    
                    foreach ($encargados as $encargado) {
                        if ($encargado->usuario && $encargado->usuario->email) {
                            $destinatarios[] = $encargado->usuario->email;
                        }
                    }
                }
                
                // Notificar al creador
                if ($nota->usuarioCrea && $nota->usuarioCrea->email) {
                    $destinatarios[] = $nota->usuarioCrea->email;
                }
                break;

            case 'recibida':
                // Notificar al creador y quien envió
                if ($nota->usuarioCrea && $nota->usuarioCrea->email) {
                    $destinatarios[] = $nota->usuarioCrea->email;
                }
                
                if ($nota->usuarioEnvia && $nota->usuarioEnvia->email) {
                    $destinatarios[] = $nota->usuarioEnvia->email;
                }
                
                // Notificar a encargados de tienda origen
                if ($nota->tiendaOrigen) {
                    $encargados = $nota->tiendaOrigen->encargados()
                        ->with('usuario')
                        ->where('activo', true)
                        ->get();
                    
                    foreach ($encargados as $encargado) {
                        if ($encargado->usuario && $encargado->usuario->email) {
                            $destinatarios[] = $encargado->usuario->email;
                        }
                    }
                }
                break;
        }

        // Remover duplicados
        return array_unique($destinatarios);
    }

    /**
     * Procesar plantilla reemplazando variables
     */
    private function procesarPlantilla(string $plantilla, NotaMovimiento $nota): string
    {
        // Generar listado de artículos HTML
        $articulosHtml = '';
        if ($nota->detallesArticulos) {
            foreach ($nota->detallesArticulos as $detalle) {
                $articulosHtml .= '<tr>';
                $articulosHtml .= '<td>' . ($detalle->articulo->codigo_barra ?? 'S/N') . '</td>';
                $articulosHtml .= '<td>' . ($detalle->articulo->nombre_articulo ?? 'N/A') . '</td>';
                $articulosHtml .= '<td style="text-align: center; font-weight: bold;">' . $detalle->cantidad . '</td>';
                $articulosHtml .= '</tr>';
            }
        }
        
        if (empty($articulosHtml)) {
            $articulosHtml = '<tr><td colspan="3" style="text-align: center;">No hay artículos</td></tr>';
        }

        $variables = [
            '{{numero_nota}}' => $nota->numero_nota,
            '{{tipo_nota}}' => $nota->tipo_nota,
            '{{fecha_creacion}}' => $nota->fecha_creacion ? $nota->fecha_creacion->format('d/m/Y H:i') : date('d/m/Y H:i'),
            '{{fecha_envio}}' => $nota->fecha_envio ? $nota->fecha_envio->format('d/m/Y H:i') : 'Pendiente',
            '{{fecha_recepcion}}' => $nota->fecha_recepcion ? $nota->fecha_recepcion->format('d/m/Y H:i') : 'Pendiente',
            '{{tienda_origen}}' => $nota->tiendaOrigen ? $nota->tiendaOrigen->nombre_tienda : ($nota->proveedor_origen ?? 'N/A'),
            '{{tienda_destino}}' => $nota->tiendaDestino ? $nota->tiendaDestino->nombre_tienda : ($nota->proveedorDestino ? $nota->proveedorDestino->nombre_proveedor : 'N/A'),
            '{{estado}}' => $nota->estado ? $nota->estado->nombre_estado : '',
            '{{usuario_crea}}' => $nota->usuarioCrea ? $nota->usuarioCrea->nombre . ' ' . $nota->usuarioCrea->apellido : '',
            '{{usuario_envia}}' => $nota->usuarioEnvia ? $nota->usuarioEnvia->nombre . ' ' . $nota->usuarioEnvia->apellido : 'Pendiente',
            '{{usuario_recibe}}' => $nota->usuarioRecibe ? $nota->usuarioRecibe->nombre . ' ' . $nota->usuarioRecibe->apellido : 'Pendiente',
            '{{metodo_envio}}' => $nota->metodoEnvio ? $nota->metodoEnvio->nombre_metodo : '',
            '{{observaciones}}' => $nota->observaciones ?? 'Sin observaciones',
            '{{articulos_listado}}' => $articulosHtml,
        ];

        return str_replace(array_keys($variables), array_values($variables), $plantilla);
    }
}