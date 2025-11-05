<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permisos_procesos_usuario', function (Blueprint $table) {
            $table->id('id_permiso_proceso');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_estado');
            $table->boolean('tiene_permiso')->default(true);
            $table->timestamp('fecha_asignacion')->useCurrent();
            $table->unsignedBigInteger('id_usuario_asigna')->nullable();
            
            // Foreign keys
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('id_estado')->references('id_estado')->on('estados_nota')->onDelete('cascade');
            $table->foreign('id_usuario_asigna')->references('id_usuario')->on('usuarios')->onDelete('set null');
            
            // Índice único para evitar duplicados
            $table->unique(['id_usuario', 'id_estado'], 'idx_usuario_estado_proceso');
            
            // Índices para búsquedas
            $table->index('id_usuario', 'idx_permisos_proceso_usuario');
            $table->index('id_estado', 'idx_permisos_proceso_estado');
            $table->index('tiene_permiso', 'idx_permisos_proceso_activo');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos_procesos_usuario');
    }
};
