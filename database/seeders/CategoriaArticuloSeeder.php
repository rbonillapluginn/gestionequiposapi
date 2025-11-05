<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaArticuloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            [
                'nombre_categoria' => 'Computadora',
                'descripcion' => 'Computadoras de escritorio',
                'activo' => true,
            ],
            [
                'nombre_categoria' => 'Laptop',
                'descripcion' => 'Computadoras portátiles',
                'activo' => true,
            ],
            [
                'nombre_categoria' => 'Monitor',
                'descripcion' => 'Monitores y pantallas',
                'activo' => true,
            ],
            [
                'nombre_categoria' => 'Impresora',
                'descripcion' => 'Impresoras y multifuncionales',
                'activo' => true,
            ],
            [
                'nombre_categoria' => 'Teléfono',
                'descripcion' => 'Teléfonos fijos y móviles',
                'activo' => true,
            ],
            [
                'nombre_categoria' => 'Tablet',
                'descripcion' => 'Tablets y dispositivos táctiles',
                'activo' => true,
            ],
            [
                'nombre_categoria' => 'Accesorio',
                'descripcion' => 'Accesorios y periféricos',
                'activo' => true,
            ],
            [
                'nombre_categoria' => 'Otro',
                'descripcion' => 'Otros equipos tecnológicos',
                'activo' => true,
            ],
        ];

        foreach ($categorias as $categoria) {
            DB::table('categorias_articulos')->updateOrInsert(
                ['nombre_categoria' => $categoria['nombre_categoria']],
                $categoria
            );
        }

        $this->command->info('✓ Categorías de artículos creadas exitosamente');
    }
}
