<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Estados de Nota
        Schema::create('estados_nota', function (Blueprint $table) {
            $table->id('id_estado');
            $table->string('nombre_estado', 50)->unique();
            $table->text('descripcion')->nullable();
            $table->integer('orden');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Notas de Movimiento (Entrada y Salida)
        Schema::create('notas_movimiento', function (Blueprint $table) {
            $table->id('id_nota');
            $table->string('numero_nota', 50)->unique();
            $table->enum('tipo_nota', ['ENTRADA', 'SALIDA']);
            $table->unsignedBigInteger('id_tipo_movimiento');
            
            // Origen y Destino
            $table->unsignedBigInteger('id_tienda_origen')->nullable();
            $table->unsignedBigInteger('id_tienda_destino')->nullable();
            $table->string('proveedor_origen', 200)->nullable();
            $table->string('proveedor_destino', 200)->nullable();
            
            // Método de Envío
            $table->unsignedBigInteger('id_metodo_envio');
            $table->unsignedBigInteger('id_submetodo_envio')->nullable();
            
            // Detalles de Transporte
            $table->unsignedBigInteger('id_vehiculo')->nullable();
            $table->unsignedBigInteger('id_chofer')->nullable();
            $table->dateTime('hora_salida')->nullable();
            $table->unsignedBigInteger('id_mensajero')->nullable();
            
            // Información General
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->dateTime('fecha_envio')->nullable();
            $table->dateTime('fecha_recepcion')->nullable();
            $table->unsignedBigInteger('id_usuario_crea');
            $table->unsignedBigInteger('id_usuario_envia')->nullable();
            $table->unsignedBigInteger('id_usuario_recibe')->nullable();
            
            // Estado y Observaciones
            $table->unsignedBigInteger('id_estado');
            $table->text('observaciones')->nullable();
            
            // Foreign Keys
            $table->foreign('id_tipo_movimiento')->references('id_tipo_movimiento')->on('tipos_movimiento');
            $table->foreign('id_tienda_origen')->references('id_tienda')->on('tiendas');
            $table->foreign('id_tienda_destino')->references('id_tienda')->on('tiendas');
            $table->foreign('id_metodo_envio')->references('id_metodo_envio')->on('metodos_envio');
            $table->foreign('id_submetodo_envio')->references('id_submetodo')->on('submetodos_envio');
            $table->foreign('id_vehiculo')->references('id_vehiculo')->on('vehiculos');
            $table->foreign('id_chofer')->references('id_chofer')->on('choferes');
            $table->foreign('id_mensajero')->references('id_mensajero')->on('mensajeros');
            $table->foreign('id_usuario_crea')->references('id_usuario')->on('usuarios');
            $table->foreign('id_usuario_envia')->references('id_usuario')->on('usuarios');
            $table->foreign('id_usuario_recibe')->references('id_usuario')->on('usuarios');
            $table->foreign('id_estado')->references('id_estado')->on('estados_nota');
            
            // Índices
            $table->index(['fecha_creacion', 'fecha_envio', 'fecha_recepcion'], 'idx_notas_fechas');
            $table->index('id_estado', 'idx_notas_estado');
            $table->index(['id_tienda_origen', 'id_tienda_destino'], 'idx_notas_tiendas');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // CHECK constraint para origen y destino
        DB::statement('
            ALTER TABLE notas_movimiento 
            ADD CONSTRAINT chk_origen_destino 
            CHECK (
                (id_tienda_origen IS NOT NULL OR proveedor_origen IS NOT NULL) AND
                (id_tienda_destino IS NOT NULL OR proveedor_destino IS NOT NULL)
            )
        ');

        // Detalle de Artículos en Notas
        Schema::create('detalle_nota_articulos', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('id_nota');
            $table->unsignedBigInteger('id_articulo');
            $table->integer('cantidad');
            $table->unsignedBigInteger('id_unidad_envio')->nullable();
            $table->text('observaciones')->nullable();
            
            $table->foreign('id_nota')->references('id_nota')->on('notas_movimiento');
            $table->foreign('id_articulo')->references('id_articulo')->on('articulos');
            $table->foreign('id_unidad_envio')->references('id_unidad_envio')->on('unidades_envio');
            $table->index('id_nota', 'idx_detalle_nota');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_nota_articulos');
        Schema::dropIfExists('notas_movimiento');
        Schema::dropIfExists('estados_nota');
    }
};