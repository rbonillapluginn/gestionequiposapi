<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermisoProcesoUsuario;
use App\Models\User;
use App\Models\EstadoNota;
use Illuminate\Support\Facades\DB;

class PermisoProcesoUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuario administrador (nivel 1)
        $admin = User::where('id_nivel_autorizacion', 1)->first();
        
        if (!$admin) {
            $this->command->warn('No se encontró usuario administrador. Asegúrate de ejecutar UserSeeder primero.');
            return;
        }

        // Obtener todos los estados
        $estados = EstadoNota::all();

        if ($estados->isEmpty()) {
            $this->command->warn('No se encontraron estados. Asegúrate de ejecutar los seeders de estados primero.');
            return;
        }

        $this->command->info('Asignando permisos de procesos al administrador...');

        // Asignar todos los permisos al administrador
        foreach ($estados as $estado) {
            PermisoProcesoUsuario::updateOrCreate(
                [
                    'id_usuario' => $admin->id_usuario,
                    'id_estado' => $estado->id_estado
                ],
                [
                    'tiene_permiso' => true,
                    'id_usuario_asigna' => $admin->id_usuario
                ]
            );
            
            $this->command->info("  ✓ Permiso asignado para: {$estado->nombre_estado}");
        }

        $this->command->info('✅ Permisos de procesos asignados exitosamente');
    }
}
