<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EstadoNotaSeeder::class,
            TipoUnidadEnvioSeeder::class,
            TipoMaterialSeeder::class,
            MetodoEnvioSeeder::class,
            CategoriaArticuloSeeder::class,
            InitialDataSeeder::class,
        ]);
    }
}
