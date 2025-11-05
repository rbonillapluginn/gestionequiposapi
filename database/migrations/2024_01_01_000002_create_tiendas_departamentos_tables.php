<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Departamentos
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id('id_departamento');
            $table->string('nombre_departamento', 100);
            $table->string('codigo_departamento', 20)->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Tiendas
        Schema::create('tiendas', function (Blueprint $table) {
            $table->id('id_tienda');
            $table->string('nombre_tienda', 100);
            $table->string('codigo_tienda', 20)->unique();
            $table->text('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Encargados de Tienda por Departamento
        Schema::create('encargados_tienda', function (Blueprint $table) {
            $table->id('id_encargado');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_tienda');
            $table->unsignedBigInteger('id_departamento');
            $table->boolean('es_principal')->default(false);
            $table->date('fecha_asignacion');
            $table->date('fecha_fin')->nullable();
            $table->boolean('activo')->default(true);
            
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios');
            $table->foreign('id_tienda')->references('id_tienda')->on('tiendas');
            $table->foreign('id_departamento')->references('id_departamento')->on('departamentos');
            $table->index(['id_tienda', 'id_departamento'], 'idx_encargados_tienda');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encargados_tienda');
        Schema::dropIfExists('tiendas');
        Schema::dropIfExists('departamentos');
    }
};