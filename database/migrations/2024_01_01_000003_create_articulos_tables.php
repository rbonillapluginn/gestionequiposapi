<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categorías de Artículos
        Schema::create('categorias_articulos', function (Blueprint $table) {
            $table->id('id_categoria');
            $table->string('nombre_categoria', 100);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Artículos (Código de Barra o Número de Serie obligatorio)
        Schema::create('articulos', function (Blueprint $table) {
            $table->id('id_articulo');
            $table->string('nombre_articulo', 200);
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('id_categoria')->nullable();
            $table->string('codigo_barra', 100)->nullable()->unique();
            $table->string('numero_serie', 100)->nullable()->unique();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->enum('estado', ['disponible', 'en_uso', 'en_reparacion', 'dado_de_baja'])->default('disponible');
            $table->text('observaciones')->nullable();
            $table->decimal('precio', 10, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_ultima_modificacion')->useCurrent()->useCurrentOnUpdate();
            
            $table->foreign('id_categoria')->references('id_categoria')->on('categorias_articulos');
            $table->index('id_categoria', 'idx_articulos_categoria');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            // CHECK constraint para asegurar que al menos uno de los campos esté presente
            $table->rawSql = 'ALTER TABLE articulos ADD CONSTRAINT chk_codigo_serie CHECK (codigo_barra IS NOT NULL OR numero_serie IS NOT NULL)';
        });
        
        // Aplicar el CHECK constraint
        DB::statement('ALTER TABLE articulos ADD CONSTRAINT chk_codigo_serie CHECK (codigo_barra IS NOT NULL OR numero_serie IS NOT NULL)');
    }

    public function down(): void
    {
        Schema::dropIfExists('articulos');
        Schema::dropIfExists('categorias_articulos');
    }
};