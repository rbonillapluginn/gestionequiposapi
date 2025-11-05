<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlantillaCorreo;

class PlantillaCorreoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plantillas = [
            // Plantilla: Nota Creada
            [
                'nombre_plantilla' => 'nota_creada',
                'asunto' => 'Nueva Nota de Movimiento Creada - {{numero_nota}}',
                'cuerpo_html' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #2196F3; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table th { background-color: #f0f0f0; padding: 10px; text-align: left; border: 1px solid #ddd; }
        .info-table td { padding: 10px; border: 1px solid #ddd; }
        .articulos-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .articulos-table th { background-color: #2196F3; color: white; padding: 10px; text-align: left; }
        .articulos-table td { padding: 8px; border-bottom: 1px solid #ddd; }
        .footer { background-color: #333; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-entrada { background-color: #4CAF50; color: white; }
        .badge-salida { background-color: #FF9800; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Nueva Nota de Movimiento Creada</h1>
        </div>
        
        <div class="content">
            <p><strong>Se ha creado una nueva nota de movimiento en el sistema:</strong></p>
            
            <table class="info-table">
                <tr>
                    <th>Número de Nota:</th>
                    <td><strong>{{numero_nota}}</strong></td>
                </tr>
                <tr>
                    <th>Tipo:</th>
                    <td><span class="badge badge-{{tipo_nota}}">{{tipo_nota}}</span></td>
                </tr>
                <tr>
                    <th>Origen:</th>
                    <td>{{tienda_origen}}</td>
                </tr>
                <tr>
                    <th>Destino:</th>
                    <td>{{tienda_destino}}</td>
                </tr>
                <tr>
                    <th>Método de Envío:</th>
                    <td>{{metodo_envio}}</td>
                </tr>
                <tr>
                    <th>Fecha de Creación:</th>
                    <td>{{fecha_creacion}}</td>
                </tr>
                <tr>
                    <th>Creado Por:</th>
                    <td>{{usuario_crea}}</td>
                </tr>
                <tr>
                    <th>Estado:</th>
                    <td>{{estado}}</td>
                </tr>
            </table>

            <h3>📦 Artículos en la Nota</h3>
            <table class="articulos-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Artículo</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    {{articulos_listado}}
                </tbody>
            </table>

            <p><strong>Observaciones:</strong><br>{{observaciones}}</p>

            <p style="margin-top: 30px; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107;">
                <strong>⚠️ Importante:</strong> Esta nota está pendiente de ser enviada. 
                Por favor, revise los detalles y proceda con el despacho cuando esté lista.
            </p>
        </div>
        
        <div class="footer">
            <p>Sistema de Gestión de Equipos - El Costo</p>
            <p>Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
                ',
                'cuerpo_texto' => 'Nueva Nota de Movimiento Creada

Número de Nota: {{numero_nota}}
Tipo: {{tipo_nota}}
Origen: {{tienda_origen}}
Destino: {{tienda_destino}}
Método de Envío: {{metodo_envio}}
Fecha de Creación: {{fecha_creacion}}
Creado Por: {{usuario_crea}}
Estado: {{estado}}

Observaciones: {{observaciones}}

Sistema de Gestión de Equipos - El Costo',
                'activo' => true,
            ],

            // Plantilla: Nota Enviada
            [
                'nombre_plantilla' => 'nota_enviada',
                'asunto' => '🚚 Nota en Tránsito: {{numero_nota}}',
                'cuerpo_html' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #FF9800; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table th { background-color: #f0f0f0; padding: 10px; text-align: left; border: 1px solid #ddd; }
        .info-table td { padding: 10px; border: 1px solid #ddd; }
        .articulos-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .articulos-table th { background-color: #FF9800; color: white; padding: 10px; text-align: left; }
        .articulos-table td { padding: 8px; border-bottom: 1px solid #ddd; }
        .footer { background-color: #333; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
        .alert { margin-top: 30px; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚚 Nota en Tránsito</h1>
        </div>
        
        <div class="content">
            <p><strong>La siguiente nota de movimiento ha sido enviada y está en tránsito:</strong></p>
            
            <table class="info-table">
                <tr>
                    <th>Número de Nota:</th>
                    <td><strong>{{numero_nota}}</strong></td>
                </tr>
                <tr>
                    <th>Tipo:</th>
                    <td>{{tipo_nota}}</td>
                </tr>
                <tr>
                    <th>Origen:</th>
                    <td>{{tienda_origen}}</td>
                </tr>
                <tr>
                    <th>Destino:</th>
                    <td>{{tienda_destino}}</td>
                </tr>
                <tr>
                    <th>Método de Envío:</th>
                    <td>{{metodo_envio}}</td>
                </tr>
                <tr>
                    <th>Fecha de Envío:</th>
                    <td><strong>{{fecha_envio}}</strong></td>
                </tr>
                <tr>
                    <th>Enviado Por:</th>
                    <td>{{usuario_envia}}</td>
                </tr>
                <tr>
                    <th>Creado Por:</th>
                    <td>{{usuario_crea}}</td>
                </tr>
            </table>

            <h3>📦 Artículos Enviados</h3>
            <table class="articulos-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Artículo</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    {{articulos_listado}}
                </tbody>
            </table>

            <p><strong>Observaciones:</strong><br>{{observaciones}}</p>

            <div class="alert">
                <strong>📍 Próximo Paso:</strong> Esta nota está en camino a su destino. 
                El encargado de la tienda destino deberá confirmar la recepción cuando los artículos lleguen.
            </div>
        </div>
        
        <div class="footer">
            <p>Sistema de Gestión de Equipos - El Costo</p>
            <p>Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
                ',
                'cuerpo_texto' => 'Nota en Tránsito

Número de Nota: {{numero_nota}}
Tipo: {{tipo_nota}}
Origen: {{tienda_origen}}
Destino: {{tienda_destino}}
Método de Envío: {{metodo_envio}}
Fecha de Envío: {{fecha_envio}}
Enviado Por: {{usuario_envia}}

Observaciones: {{observaciones}}

Sistema de Gestión de Equipos - El Costo',
                'activo' => true,
            ],

            // Plantilla: Nota Recibida
            [
                'nombre_plantilla' => 'nota_recibida',
                'asunto' => '✅ Nota Recibida: {{numero_nota}}',
                'cuerpo_html' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table th { background-color: #f0f0f0; padding: 10px; text-align: left; border: 1px solid #ddd; }
        .info-table td { padding: 10px; border: 1px solid #ddd; }
        .articulos-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .articulos-table th { background-color: #4CAF50; color: white; padding: 10px; text-align: left; }
        .articulos-table td { padding: 8px; border-bottom: 1px solid #ddd; }
        .footer { background-color: #333; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 5px 5px; }
        .success-box { margin-top: 30px; padding: 15px; background-color: #d4edda; border-left: 4px solid #28a745; }
        .timeline { margin: 20px 0; padding-left: 20px; border-left: 3px solid #4CAF50; }
        .timeline-item { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Nota Recibida Exitosamente</h1>
        </div>
        
        <div class="content">
            <p><strong>La siguiente nota de movimiento ha sido recibida en su destino:</strong></p>
            
            <table class="info-table">
                <tr>
                    <th>Número de Nota:</th>
                    <td><strong>{{numero_nota}}</strong></td>
                </tr>
                <tr>
                    <th>Tipo:</th>
                    <td>{{tipo_nota}}</td>
                </tr>
                <tr>
                    <th>Origen:</th>
                    <td>{{tienda_origen}}</td>
                </tr>
                <tr>
                    <th>Destino:</th>
                    <td>{{tienda_destino}}</td>
                </tr>
                <tr>
                    <th>Método de Envío:</th>
                    <td>{{metodo_envio}}</td>
                </tr>
            </table>

            <h3>📅 Línea de Tiempo</h3>
            <div class="timeline">
                <div class="timeline-item">
                    <strong>Creación:</strong> {{fecha_creacion}} por {{usuario_crea}}
                </div>
                <div class="timeline-item">
                    <strong>Envío:</strong> {{fecha_envio}} por {{usuario_envia}}
                </div>
                <div class="timeline-item">
                    <strong>Recepción:</strong> {{fecha_recepcion}} por {{usuario_recibe}}
                </div>
            </div>

            <h3>📦 Artículos Recibidos</h3>
            <table class="articulos-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Artículo</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    {{articulos_listado}}
                </tbody>
            </table>

            <p><strong>Observaciones:</strong><br>{{observaciones}}</p>

            <div class="success-box">
                <strong>✅ Proceso Completado:</strong> La nota de movimiento ha finalizado su ciclo exitosamente. 
                Los artículos han sido entregados y el inventario ha sido actualizado.
            </div>
        </div>
        
        <div class="footer">
            <p>Sistema de Gestión de Equipos - El Costo</p>
            <p>Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
                ',
                'cuerpo_texto' => 'Nota Recibida Exitosamente

Número de Nota: {{numero_nota}}
Tipo: {{tipo_nota}}
Origen: {{tienda_origen}}
Destino: {{tienda_destino}}
Método de Envío: {{metodo_envio}}

Creación: {{fecha_creacion}} por {{usuario_crea}}
Envío: {{fecha_envio}} por {{usuario_envia}}
Recepción: {{fecha_recepcion}} por {{usuario_recibe}}

Observaciones: {{observaciones}}

Proceso Completado: Los artículos han sido entregados exitosamente.

Sistema de Gestión de Equipos - El Costo',
                'activo' => true,
            ],
        ];

        foreach ($plantillas as $plantilla) {
            PlantillaCorreo::updateOrCreate(
                ['nombre_plantilla' => $plantilla['nombre_plantilla']],
                $plantilla
            );
        }

        $this->command->info('✅ Plantillas de correo actualizadas exitosamente');
    }
}
