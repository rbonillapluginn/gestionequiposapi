<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodoEnvioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertar o actualizar métodos de envío
        DB::table('metodos_envio')->updateOrInsert(
            ['nombre_metodo' => 'CAMION'],
            [
                'nombre_metodo' => 'CAMION',
                'descripcion' => 'Envío por camión de la empresa',
                'requiere_vehiculo' => true,
                'requiere_mensajero' => false,
                'activo' => true
            ]
        );

        DB::table('metodos_envio')->updateOrInsert(
            ['nombre_metodo' => 'MENSAJERIA_INTERNA'],
            [
                'nombre_metodo' => 'MENSAJERIA_INTERNA',
                'descripcion' => 'Mensajería interna de la empresa',
                'requiere_vehiculo' => false,
                'requiere_mensajero' => true,
                'activo' => true
            ]
        );

        DB::table('metodos_envio')->updateOrInsert(
            ['nombre_metodo' => 'OTRO'],
            [
                'nombre_metodo' => 'OTRO',
                'descripcion' => 'Otro método de envío',
                'requiere_vehiculo' => false,
                'requiere_mensajero' => false,
                'activo' => true
            ]
        );

        // Obtener ID de mensajería para submétodos
        $mensajeriaId = DB::table('metodos_envio')
            ->where('nombre_metodo', 'MENSAJERIA_INTERNA')
            ->value('id_metodo_envio');

        // Submétodos para mensajería
        if ($mensajeriaId) {
            DB::table('submetodos_envio')->updateOrInsert(
                [
                    'id_metodo_envio' => $mensajeriaId,
                    'nombre_submetodo' => 'DIRECTO'
                ],
                [
                    'id_metodo_envio' => $mensajeriaId,
                    'nombre_submetodo' => 'DIRECTO',
                    'descripcion' => 'Entrega directa al destino',
                    'requiere_mensajero' => true,
                    'activo' => true
                ]
            );

            DB::table('submetodos_envio')->updateOrInsert(
                [
                    'id_metodo_envio' => $mensajeriaId,
                    'nombre_submetodo' => 'RECORRIDO'
                ],
                [
                    'id_metodo_envio' => $mensajeriaId,
                    'nombre_submetodo' => 'RECORRIDO',
                    'descripcion' => 'Entrega por recorrido con múltiples paradas',
                    'requiere_mensajero' => true,
                    'activo' => true
                ]
            );
        }
    }
}