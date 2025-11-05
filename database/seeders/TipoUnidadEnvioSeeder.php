<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoUnidadEnvioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            [
                'nombre_tipo' => 'CAJA',
                'descripcion' => 'Caja para empaque',
                'activo' => true
            ],
            [
                'nombre_tipo' => 'SOBRE',
                'descripcion' => 'Sobre de documentos o artículos pequeños',
                'activo' => true
            ],
            [
                'nombre_tipo' => 'BULTO',
                'descripcion' => 'Bulto general',
                'activo' => true
            ],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipos_unidad_envio')->updateOrInsert(
                ['nombre_tipo' => $tipo['nombre_tipo']],
                $tipo
            );
        }
    }
}