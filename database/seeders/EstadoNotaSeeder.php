<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoNotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            [
                'nombre_estado' => 'CREADA',
                'descripcion' => 'Nota creada pero no enviada',
                'orden' => 1
            ],
            [
                'nombre_estado' => 'EN_TRANSITO',
                'descripcion' => 'Nota enviada, en camino',
                'orden' => 2
            ],
            [
                'nombre_estado' => 'RECIBIDA',
                'descripcion' => 'Nota recibida en destino',
                'orden' => 3
            ],
            [
                'nombre_estado' => 'CANCELADA',
                'descripcion' => 'Nota cancelada',
                'orden' => 4
            ],
        ];

        foreach ($estados as $estado) {
            DB::table('estados_nota')->updateOrInsert(
                ['nombre_estado' => $estado['nombre_estado']],
                $estado
            );
        }
    }
}