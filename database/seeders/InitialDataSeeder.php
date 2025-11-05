<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Niveles de autorización
        DB::table('niveles_autorizacion')->updateOrInsert(
            ['nombre_nivel' => 'Super Administrador'],
            [
                'nombre_nivel' => 'Super Administrador',
                'descripcion' => 'Acceso total al sistema',
                'orden_jerarquico' => 1,
                'activo' => true
            ]
        );

        DB::table('niveles_autorizacion')->updateOrInsert(
            ['nombre_nivel' => 'Administrador'],
            [
                'nombre_nivel' => 'Administrador',
                'descripcion' => 'Acceso administrativo',
                'orden_jerarquico' => 2,
                'activo' => true
            ]
        );

        DB::table('niveles_autorizacion')->updateOrInsert(
            ['nombre_nivel' => 'Encargado de Tienda'],
            [
                'nombre_nivel' => 'Encargado de Tienda',
                'descripcion' => 'Encargado de tienda con permisos limitados',
                'orden_jerarquico' => 3,
                'activo' => true
            ]
        );

        DB::table('niveles_autorizacion')->updateOrInsert(
            ['nombre_nivel' => 'Usuario Regular'],
            [
                'nombre_nivel' => 'Usuario Regular',
                'descripcion' => 'Usuario con permisos básicos',
                'orden_jerarquico' => 4,
                'activo' => true
            ]
        );

        // Obtener IDs
        $superAdminId = DB::table('niveles_autorizacion')->where('nombre_nivel', 'Super Administrador')->value('id_nivel');
        $administradorId = DB::table('niveles_autorizacion')->where('nombre_nivel', 'Administrador')->value('id_nivel');
        $encargadoId = DB::table('niveles_autorizacion')->where('nombre_nivel', 'Encargado de Tienda')->value('id_nivel');

        // Usuario administrador por defecto
        DB::table('usuarios')->updateOrInsert(
            ['username' => 'admin'],
            [
                'username' => 'admin',
                'password_hash' => Hash::make('admin123'),
                'nombre' => 'Administrador',
                'apellido' => 'del Sistema',
                'email' => 'admin@sistema.com',
                'telefono' => '0000-0000',
                'id_nivel_autorizacion' => $superAdminId,
                'activo' => true
            ]
        );

        // Secciones del sistema
        DB::table('secciones')->updateOrInsert(
            ['codigo_seccion' => 'USUARIOS'],
            [
                'nombre_seccion' => 'Usuarios',
                'descripcion' => 'Gestión de usuarios del sistema',
                'codigo_seccion' => 'USUARIOS',
                'activo' => true
            ]
        );

        DB::table('secciones')->updateOrInsert(
            ['codigo_seccion' => 'NOTAS'],
            [
                'nombre_seccion' => 'Notas de Movimiento',
                'descripcion' => 'Creación y gestión de notas de entrada/salida',
                'codigo_seccion' => 'NOTAS',
                'activo' => true
            ]
        );

        DB::table('secciones')->updateOrInsert(
            ['codigo_seccion' => 'ARTICULOS'],
            [
                'nombre_seccion' => 'Artículos',
                'descripcion' => 'Gestión de artículos e inventario',
                'codigo_seccion' => 'ARTICULOS',
                'activo' => true
            ]
        );

        DB::table('secciones')->updateOrInsert(
            ['codigo_seccion' => 'TIENDAS'],
            [
                'nombre_seccion' => 'Tiendas',
                'descripcion' => 'Gestión de tiendas y departamentos',
                'codigo_seccion' => 'TIENDAS',
                'activo' => true
            ]
        );

        DB::table('secciones')->updateOrInsert(
            ['codigo_seccion' => 'CATALOGOS'],
            [
                'nombre_seccion' => 'Catálogos',
                'descripcion' => 'Gestión de catálogos del sistema',
                'codigo_seccion' => 'CATALOGOS',
                'activo' => true
            ]
        );

        DB::table('secciones')->updateOrInsert(
            ['codigo_seccion' => 'PERMISOS'],
            [
                'nombre_seccion' => 'Permisos',
                'descripcion' => 'Gestión de permisos y autorizaciones',
                'codigo_seccion' => 'PERMISOS',
                'activo' => true
            ]
        );

        // Obtener IDs de secciones
        $seccionUsuarios = DB::table('secciones')->where('codigo_seccion', 'USUARIOS')->value('id_seccion');
        $seccionNotas = DB::table('secciones')->where('codigo_seccion', 'NOTAS')->value('id_seccion');
        $seccionArticulos = DB::table('secciones')->where('codigo_seccion', 'ARTICULOS')->value('id_seccion');
        $seccionTiendas = DB::table('secciones')->where('codigo_seccion', 'TIENDAS')->value('id_seccion');
        $seccionCatalogos = DB::table('secciones')->where('codigo_seccion', 'CATALOGOS')->value('id_seccion');
        $seccionPermisos = DB::table('secciones')->where('codigo_seccion', 'PERMISOS')->value('id_seccion');

        // Permisos para Super Administrador (acceso total)
        $secciones = [$seccionUsuarios, $seccionNotas, $seccionArticulos, $seccionTiendas, $seccionCatalogos, $seccionPermisos];
        foreach ($secciones as $seccionId) {
            DB::table('permisos_secciones')->updateOrInsert(
                [
                    'id_nivel_autorizacion' => $superAdminId,
                    'id_seccion' => $seccionId
                ],
                [
                    'id_nivel_autorizacion' => $superAdminId,
                    'id_seccion' => $seccionId,
                    'puede_leer' => true,
                    'puede_crear' => true,
                    'puede_modificar' => true,
                    'puede_eliminar' => true
                ]
            );
        }

        // Tipos de movimiento
        DB::table('tipos_movimiento')->updateOrInsert(
            ['codigo_tipo' => 'TRANSFER'],
            [
                'nombre_tipo' => 'Transferencia entre tiendas',
                'codigo_tipo' => 'TRANSFER',
                'descripcion' => 'Movimiento de artículos entre tiendas',
                'activo' => true
            ]
        );

        DB::table('tipos_movimiento')->updateOrInsert(
            ['codigo_tipo' => 'DEV_PROV'],
            [
                'nombre_tipo' => 'Devolución a proveedor',
                'codigo_tipo' => 'DEV_PROV',
                'descripcion' => 'Devolución de artículos a proveedor',
                'activo' => true
            ]
        );

        DB::table('tipos_movimiento')->updateOrInsert(
            ['codigo_tipo' => 'REC_PROV'],
            [
                'nombre_tipo' => 'Recepción de proveedor',
                'codigo_tipo' => 'REC_PROV',
                'descripcion' => 'Recepción de artículos de proveedor',
                'activo' => true
            ]
        );

        // Plantillas de correo
        DB::table('plantillas_correo')->updateOrInsert(
            ['nombre_plantilla' => 'nota_creada'],
            [
                'nombre_plantilla' => 'nota_creada',
                'asunto' => 'Nueva Nota de Movimiento Creada - {{numero_nota}}',
                'cuerpo_html' => '<h2>Nueva Nota de Movimiento</h2>
                    <p>Se ha creado una nueva nota de movimiento con los siguientes detalles:</p>
                    <ul>
                        <li><strong>Número de Nota:</strong> {{numero_nota}}</li>
                        <li><strong>Tipo:</strong> {{tipo_nota}}</li>
                        <li><strong>Origen:</strong> {{tienda_origen}}</li>
                        <li><strong>Destino:</strong> {{tienda_destino}}</li>
                        <li><strong>Creada por:</strong> {{usuario_crea}}</li>
                        <li><strong>Fecha de Creación:</strong> {{fecha_creacion}}</li>
                    </ul>
                    <p><strong>Observaciones:</strong> {{observaciones}}</p>',
                'cuerpo_texto' => 'Nueva Nota de Movimiento: {{numero_nota}} - Tipo: {{tipo_nota}} - De: {{tienda_origen}} - Para: {{tienda_destino}}',
                'activo' => true
            ]
        );

        DB::table('plantillas_correo')->updateOrInsert(
            ['nombre_plantilla' => 'nota_enviada'],
            [
                'nombre_plantilla' => 'nota_enviada',
                'asunto' => 'Nota de Movimiento Enviada - {{numero_nota}}',
                'cuerpo_html' => '<h2>Nota de Movimiento Enviada</h2>
                    <p>La nota de movimiento {{numero_nota}} ha sido enviada:</p>
                    <ul>
                        <li><strong>Número de Nota:</strong> {{numero_nota}}</li>
                        <li><strong>Tipo:</strong> {{tipo_nota}}</li>
                        <li><strong>Origen:</strong> {{tienda_origen}}</li>
                        <li><strong>Destino:</strong> {{tienda_destino}}</li>
                        <li><strong>Enviada por:</strong> {{usuario_envia}}</li>
                        <li><strong>Fecha de Envío:</strong> {{fecha_envio}}</li>
                        <li><strong>Método de Envío:</strong> {{metodo_envio}}</li>
                    </ul>
                    <p><strong>Observaciones:</strong> {{observaciones}}</p>',
                'cuerpo_texto' => 'Nota Enviada: {{numero_nota}} - De: {{tienda_origen}} - Para: {{tienda_destino}} - Fecha: {{fecha_envio}}',
                'activo' => true
            ]
        );

        DB::table('plantillas_correo')->updateOrInsert(
            ['nombre_plantilla' => 'nota_recibida'],
            [
                'nombre_plantilla' => 'nota_recibida',
                'asunto' => 'Nota de Movimiento Recibida - {{numero_nota}}',
                'cuerpo_html' => '<h2>Nota de Movimiento Recibida</h2>
                    <p>La nota de movimiento {{numero_nota}} ha sido recibida:</p>
                    <ul>
                        <li><strong>Número de Nota:</strong> {{numero_nota}}</li>
                        <li><strong>Tipo:</strong> {{tipo_nota}}</li>
                        <li><strong>Origen:</strong> {{tienda_origen}}</li>
                        <li><strong>Destino:</strong> {{tienda_destino}}</li>
                        <li><strong>Recibida por:</strong> {{usuario_recibe}}</li>
                        <li><strong>Fecha de Recepción:</strong> {{fecha_recepcion}}</li>
                    </ul>
                    <p><strong>Observaciones:</strong> {{observaciones}}</p>',
                'cuerpo_texto' => 'Nota Recibida: {{numero_nota}} - De: {{tienda_origen}} - En: {{tienda_destino}} - Fecha: {{fecha_recepcion}}',
                'activo' => true
            ]
        );
    }
}