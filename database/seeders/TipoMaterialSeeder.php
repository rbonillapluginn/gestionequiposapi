<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materiales = [
            [
                'nombre_material' => 'CARTON',
                'requiere_color' => false,
                'requiere_cintillo' => false,
                'activo' => true
            ],
            [
                'nombre_material' => 'PLASTICO',
                'requiere_color' => true,
                'requiere_cintillo' => true,
                'activo' => true
            ],
        ];

        foreach ($materiales as $material) {
            DB::table('tipos_material')->updateOrInsert(
                ['nombre_material' => $material['nombre_material']],
                $material
            );
        }
    }
}