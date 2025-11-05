<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Plantillas de Correo
        Schema::create('plantillas_correo', function (Blueprint $table) {
            $table->id('id_plantilla');
            $table->string('nombre_plantilla', 100)->unique();
            $table->string('asunto', 200);
            $table->text('cuerpo_html');
            $table->text('cuerpo_texto')->nullable();
            $table->boolean('activo')->default(true);
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Log de Correos Enviados
        Schema::create('log_correos', function (Blueprint $table) {
            $table->id('id_log_correo');
            $table->unsignedBigInteger('id_nota');
            $table->unsignedBigInteger('id_plantilla');
            $table->text('destinatarios');
            $table->string('asunto', 200);
            $table->boolean('enviado')->default(false);
            $table->timestamp('fecha_envio')->nullable();
            $table->text('error')->nullable();
            
            $table->foreign('id_nota')->references('id_nota')->on('notas_movimiento');
            $table->foreign('id_plantilla')->references('id_plantilla')->on('plantillas_correo');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Historial de Cambios de Estado
        Schema::create('historial_estados_nota', function (Blueprint $table) {
            $table->id('id_historial');
            $table->unsignedBigInteger('id_nota');
            $table->unsignedBigInteger('id_estado_anterior')->nullable();
            $table->unsignedBigInteger('id_estado_nuevo');
            $table->unsignedBigInteger('id_usuario');
            $table->timestamp('fecha_cambio')->useCurrent();
            $table->text('observaciones')->nullable();
            
            $table->foreign('id_nota')->references('id_nota')->on('notas_movimiento');
            $table->foreign('id_estado_anterior')->references('id_estado')->on('estados_nota');
            $table->foreign('id_estado_nuevo')->references('id_estado')->on('estados_nota');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios');
            $table->index('id_nota', 'idx_historial_nota');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_estados_nota');
        Schema::dropIfExists('log_correos');
        Schema::dropIfExists('plantillas_correo');
    }
};