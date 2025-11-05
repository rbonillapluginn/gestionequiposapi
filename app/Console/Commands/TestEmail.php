<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\LogCorreo;

class TestEmail extends Command
{
    protected $signature = 'test:email {email}';
    protected $description = 'Probar envío de correo';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Probando envío a: {$email}");
        
        try {
            Mail::html('<h1>Correo de Prueba</h1><p>Si ves este mensaje, el correo funciona correctamente.</p>', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Prueba de Correo - Gestión Equipos');
            });
            
            $this->info("✓ Correo enviado exitosamente a {$email}");
            
            // Mostrar últimos logs
            $this->info("\nÚltimos logs de correo:");
            $logs = LogCorreo::orderBy('id_log_correo', 'desc')->take(3)->get();
            
            if ($logs->isEmpty()) {
                $this->warn("No hay logs de correo registrados");
            } else {
                foreach ($logs as $log) {
                    $this->line("- ID: {$log->id_log_correo}, Destinatarios: {$log->destinatarios}, Enviado: " . ($log->enviado ? 'SÍ' : 'NO') . ", Error: " . ($log->error ?? 'N/A'));
                }
            }
            
        } catch (\Exception $e) {
            $this->error("✗ Error al enviar correo: " . $e->getMessage());
        }
    }
}
