<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NotaMovimiento;
use App\Models\LogCorreo;
use App\Models\PlantillaCorreo;
use App\Models\User;
use App\Services\NotificationService;

class DiagnosticarNotificaciones extends Command
{
    protected $signature = 'diagnosticar:notificaciones {id_nota?}';
    protected $description = 'Diagnosticar sistema de notificaciones por correo';

    public function handle()
    {
        $this->info("=== DIAGNÓSTICO DEL SISTEMA DE NOTIFICACIONES ===\n");
        
        // 1. Verificar plantillas
        $this->info("1. Verificando plantillas de correo:");
        $plantillas = PlantillaCorreo::where('activo', true)->get();
        if ($plantillas->isEmpty()) {
            $this->error("   ✗ No hay plantillas activas");
        } else {
            foreach ($plantillas as $plantilla) {
                $this->line("   ✓ {$plantilla->nombre_plantilla} - {$plantilla->asunto}");
            }
        }
        
        // 2. Verificar usuarios con email
        $this->info("\n2. Verificando usuarios con email:");
        $usuariosConEmail = User::whereNotNull('email')->count();
        $usuariosSinEmail = User::whereNull('email')->count();
        $this->line("   ✓ Usuarios con email: {$usuariosConEmail}");
        if ($usuariosSinEmail > 0) {
            $this->warn("   ⚠ Usuarios sin email: {$usuariosSinEmail}");
        }
        
        // 3. Verificar configuración de correo
        $this->info("\n3. Verificando configuración de correo (.env):");
        $this->line("   MAIL_HOST: " . config('mail.mailers.smtp.host'));
        $this->line("   MAIL_PORT: " . config('mail.mailers.smtp.port'));
        $this->line("   MAIL_USERNAME: " . config('mail.mailers.smtp.username'));
        $this->line("   MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption'));
        $this->line("   MAIL_FROM_ADDRESS: " . config('mail.from.address'));
        $this->line("   MAIL_FROM_NAME: " . config('mail.from.name'));
        
        // 4. Verificar logs de correo
        $this->info("\n4. Verificando logs de correo:");
        $totalLogs = LogCorreo::count();
        $enviados = LogCorreo::where('enviado', true)->count();
        $fallidos = LogCorreo::where('enviado', false)->count();
        
        $this->line("   Total intentos: {$totalLogs}");
        $this->line("   Enviados: {$enviados}");
        if ($fallidos > 0) {
            $this->warn("   Fallidos: {$fallidos}");
            
            // Mostrar últimos errores
            $errores = LogCorreo::where('enviado', false)
                ->whereNotNull('error')
                ->orderBy('id_log_correo', 'desc')
                ->take(3)
                ->get();
                
            if ($errores->isNotEmpty()) {
                $this->warn("\n   Últimos errores:");
                foreach ($errores as $error) {
                    $this->line("   - ID {$error->id_log_correo}: {$error->error}");
                }
            }
        }
        
        // 5. Si se especificó una nota, probar notificación
        if ($idNota = $this->argument('id_nota')) {
            $this->info("\n5. Probando notificación para nota #{$idNota}:");
            
            $nota = NotaMovimiento::with([
                'detallesArticulos.articulo',
                'usuarioCrea',
                'tiendaOrigen',
                'tiendaDestino',
                'proveedorDestino',
                'metodoEnvio',
                'estado'
            ])->find($idNota);
            
            if (!$nota) {
                $this->error("   ✗ Nota no encontrada");
                return;
            }
            
            $this->line("   Nota: {$nota->numero_nota}");
            $this->line("   Estado: {$nota->estado->nombre_estado}");
            $this->line("   Usuario crea: " . ($nota->usuarioCrea ? $nota->usuarioCrea->nombre . ' ' . $nota->usuarioCrea->apellido . " ({$nota->usuarioCrea->email})" : 'N/A'));
            
            // Obtener destinatarios
            $service = new NotificationService();
            $reflection = new \ReflectionClass($service);
            $method = $reflection->getMethod('obtenerDestinatarios');
            $method->setAccessible(true);
            
            $destinatarios = $method->invoke($service, $nota, 'creada');
            
            if (empty($destinatarios)) {
                $this->warn("   ⚠ No hay destinatarios configurados para esta nota");
                $this->line("   Razones posibles:");
                $this->line("   - El usuario creador no tiene email");
                $this->line("   - La tienda destino no tiene encargados con email");
            } else {
                $this->line("   ✓ Destinatarios encontrados:");
                foreach ($destinatarios as $dest) {
                    $this->line("     - {$dest}");
                }
                
                if ($this->confirm('¿Desea enviar una notificación de prueba a estos destinatarios?', false)) {
                    try {
                        $service->notificarNotaCreada($nota);
                        $this->info("   ✓ Notificación enviada exitosamente");
                    } catch (\Exception $e) {
                        $this->error("   ✗ Error al enviar: " . $e->getMessage());
                    }
                }
            }
        }
        
        $this->info("\n=== FIN DEL DIAGNÓSTICO ===");
    }
}
