<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de Colores
        Schema::create('colores', function (Blueprint $table) {
            $table->id('id_color');
            $table->string('nombre_color', 50)->unique();
            $table->string('codigo_hex', 7)->nullable();
            $table->boolean('activo')->default(true);
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Tipos de Unidad de Envío (Caja, Sobre, Bulto)
        Schema::create('tipos_unidad_envio', function (Blueprint $table) {
            $table->id('id_tipo_unidad');
            $table->string('nombre_tipo', 50)->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Tipos de Material (para Cajas: Cartón, Plástico)
        Schema::create('tipos_material', function (Blueprint $table) {
            $table->id('id_tipo_material');
            $table->string('nombre_material', 50)->unique();
            $table->boolean('requiere_color')->default(false);
            $table->boolean('requiere_cintillo')->default(false);
            $table->boolean('activo')->default(true);
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Unidades de Envío
        Schema::create('unidades_envio', function (Blueprint $table) {
            $table->id('id_unidad_envio');
            $table->unsignedBigInteger('id_tipo_unidad');
            $table->unsignedBigInteger('id_tipo_material')->nullable();
            $table->unsignedBigInteger('id_color')->nullable();
            $table->boolean('tiene_cintillo')->default(false);
            $table->string('dimensiones', 50)->nullable();
            $table->decimal('peso_maximo', 10, 2)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            
            $table->foreign('id_tipo_unidad')->references('id_tipo_unidad')->on('tipos_unidad_envio');
            $table->foreign('id_tipo_material')->references('id_tipo_material')->on('tipos_material');
            $table->foreign('id_color')->references('id_color')->on('colores');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades_envio');
        Schema::dropIfExists('tipos_material');
        Schema::dropIfExists('tipos_unidad_envio');
        Schema::dropIfExists('colores');
    }
};