<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Métodos de Envío (Camión, Mensajería Interna, Otro)
        Schema::create('metodos_envio', function (Blueprint $table) {
            $table->id('id_metodo_envio');
            $table->string('nombre_metodo', 50)->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('requiere_vehiculo')->default(false);
            $table->boolean('requiere_mensajero')->default(false);
            $table->boolean('activo')->default(true);
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Sub-métodos de Envío (para Mensajería: Directo, Recorrido)
        Schema::create('submetodos_envio', function (Blueprint $table) {
            $table->id('id_submetodo');
            $table->unsignedBigInteger('id_metodo_envio');
            $table->string('nombre_submetodo', 50);
            $table->text('descripcion')->nullable();
            $table->boolean('requiere_mensajero')->default(false);
            $table->boolean('activo')->default(true);
            
            $table->foreign('id_metodo_envio')->references('id_metodo_envio')->on('metodos_envio');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Vehículos (Camiones)
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id('id_vehiculo');
            $table->string('numero_camion', 50)->unique();
            $table->string('placa', 20)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->decimal('capacidad_carga', 10, 2)->nullable();
            $table->boolean('activo')->default(true);
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Choferes
        Schema::create('choferes', function (Blueprint $table) {
            $table->id('id_chofer');
            $table->string('nombre_completo', 200);
            $table->string('licencia', 50)->unique();
            $table->string('telefono', 20)->nullable();
            $table->boolean('activo')->default(true);
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // Mensajeros
        Schema::create('mensajeros', function (Blueprint $table) {
            $table->id('id_mensajero');
            $table->string('nombre_completo', 200);
            $table->string('telefono', 20)->nullable();
            $table->string('identificacion', 50)->unique();
            $table->boolean('activo')->default(true);
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajeros');
        Schema::dropIfExists('choferes');
        Schema::dropIfExists('vehiculos');
        Schema::dropIfExists('submetodos_envio');
        Schema::dropIfExists('metodos_envio');
    }
};