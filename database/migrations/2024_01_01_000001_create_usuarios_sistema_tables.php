<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Niveles de Autorización
        Schema::create('niveles_autorizacion', function (Blueprint $table) {
            $table->id('id_nivel');
            $table->string('nombre_nivel', 50)->unique();
            $table->text('descripcion')->nullable();
            $table->integer('orden_jerarquico');
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Usuarios
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('username', 50)->unique();
            $table->string('password_hash', 255);
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('email', 100)->unique();
            $table->string('telefono', 20)->nullable();
            $table->unsignedBigInteger('id_nivel_autorizacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_ultima_modificacion')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('ultimo_login')->nullable();
            
            $table->foreign('id_nivel_autorizacion')->references('id_nivel')->on('niveles_autorizacion');
            $table->index('id_nivel_autorizacion', 'idx_usuarios_nivel');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Secciones del Sistema
        Schema::create('secciones', function (Blueprint $table) {
            $table->id('id_seccion');
            $table->string('nombre_seccion', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->string('codigo_seccion', 20)->unique();
            $table->boolean('activo')->default(true);
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Permisos para Acceso a Secciones
        Schema::create('permisos_secciones', function (Blueprint $table) {
            $table->id('id_permiso_seccion');
            $table->unsignedBigInteger('id_nivel_autorizacion');
            $table->unsignedBigInteger('id_seccion');
            $table->boolean('puede_leer')->default(false);
            $table->boolean('puede_crear')->default(false);
            $table->boolean('puede_modificar')->default(false);
            $table->boolean('puede_eliminar')->default(false);
            
            $table->foreign('id_nivel_autorizacion')->references('id_nivel')->on('niveles_autorizacion');
            $table->foreign('id_seccion')->references('id_seccion')->on('secciones');
            $table->unique(['id_nivel_autorizacion', 'id_seccion'], 'unique_nivel_seccion');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Tipos de Movimiento
        Schema::create('tipos_movimiento', function (Blueprint $table) {
            $table->id('id_tipo_movimiento');
            $table->string('nombre_tipo', 50)->unique();
            $table->text('descripcion')->nullable();
            $table->string('codigo_tipo', 20)->unique();
            $table->boolean('activo')->default(true);
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Permisos para Tipos de Movimiento
        Schema::create('permisos_tipos_movimiento', function (Blueprint $table) {
            $table->id('id_permiso_movimiento');
            $table->unsignedBigInteger('id_nivel_autorizacion');
            $table->unsignedBigInteger('id_tipo_movimiento');
            $table->boolean('puede_ejecutar')->default(false);
            $table->boolean('requiere_autorizacion')->default(false);
            
            $table->foreign('id_nivel_autorizacion')->references('id_nivel')->on('niveles_autorizacion');
            $table->foreign('id_tipo_movimiento')->references('id_tipo_movimiento')->on('tipos_movimiento');
            $table->unique(['id_nivel_autorizacion', 'id_tipo_movimiento'], 'unique_nivel_tipo');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_tipos_movimiento');
        Schema::dropIfExists('tipos_movimiento');
        Schema::dropIfExists('permisos_secciones');
        Schema::dropIfExists('secciones');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('niveles_autorizacion');
    }
};