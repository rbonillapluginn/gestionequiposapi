<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PermisoSeccion;
use App\Models\PermisoTipoMovimiento;
use App\Models\NivelAutorizacion;
use App\Models\Seccion;
use App\Models\TipoMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Obtener permisos de secciones por nivel de autorización
     */
    public function getPermisosSecciones($idNivel)
    {
        $nivel = NivelAutorizacion::find($idNivel);
        
        if (!$nivel) {
            return response()->json([
                'success' => false,
                'message' => 'Nivel de autorización no encontrado'
            ], 404);
        }

        $permisos = PermisoSeccion::with('seccion')
            ->where('id_nivel_autorizacion', $idNivel)
            ->get();

        // Obtener todas las secciones para mostrar las que no tienen permisos asignados
        $secciones = Seccion::where('activo', true)->get();
        $seccionesConPermisos = $permisos->pluck('id_seccion')->toArray();
        
        $seccionesSinPermisos = $secciones->filter(function($seccion) use ($seccionesConPermisos) {
            return !in_array($seccion->id_seccion, $seccionesConPermisos);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'nivel' => $nivel,
                'permisos_asignados' => $permisos,
                'secciones_sin_permisos' => $seccionesSinPermisos
            ]
        ]);
    }

    /**
     * Asignar o actualizar permisos de secciones
     */
    public function updatePermisosSecciones(Request $request, $idNivel)
    {
        $validator = Validator::make($request->all(), [
            'permisos' => 'required|array',
            'permisos.*.id_seccion' => 'required|exists:secciones,id_seccion',
            'permisos.*.puede_leer' => 'boolean',
            'permisos.*.puede_crear' => 'boolean',
            'permisos.*.puede_modificar' => 'boolean',
            'permisos.*.puede_eliminar' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $nivel = NivelAutorizacion::find($idNivel);
        
        if (!$nivel) {
            return response()->json([
                'success' => false,
                'message' => 'Nivel de autorización no encontrado'
            ], 404);
        }

        foreach ($request->permisos as $permiso) {
            PermisoSeccion::updateOrCreate(
                [
                    'id_nivel_autorizacion' => $idNivel,
                    'id_seccion' => $permiso['id_seccion']
                ],
                [
                    'puede_leer' => $permiso['puede_leer'] ?? false,
                    'puede_crear' => $permiso['puede_crear'] ?? false,
                    'puede_modificar' => $permiso['puede_modificar'] ?? false,
                    'puede_eliminar' => $permiso['puede_eliminar'] ?? false,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Permisos actualizados exitosamente'
        ]);
    }

    /**
     * Obtener permisos de tipos de movimiento por nivel de autorización
     */
    public function getPermisosTiposMovimiento($idNivel)
    {
        $nivel = NivelAutorizacion::find($idNivel);
        
        if (!$nivel) {
            return response()->json([
                'success' => false,
                'message' => 'Nivel de autorización no encontrado'
            ], 404);
        }

        $permisos = PermisoTipoMovimiento::with('tipoMovimiento')
            ->where('id_nivel_autorizacion', $idNivel)
            ->get();

        // Obtener todos los tipos de movimiento
        $tiposMovimiento = TipoMovimiento::where('activo', true)->get();
        $tiposConPermisos = $permisos->pluck('id_tipo_movimiento')->toArray();
        
        $tiposSinPermisos = $tiposMovimiento->filter(function($tipo) use ($tiposConPermisos) {
            return !in_array($tipo->id_tipo_movimiento, $tiposConPermisos);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'nivel' => $nivel,
                'permisos_asignados' => $permisos,
                'tipos_sin_permisos' => $tiposSinPermisos
            ]
        ]);
    }

    /**
     * Asignar o actualizar permisos de tipos de movimiento
     */
    public function updatePermisosTiposMovimiento(Request $request, $idNivel)
    {
        $validator = Validator::make($request->all(), [
            'permisos' => 'required|array',
            'permisos.*.id_tipo_movimiento' => 'required|exists:tipos_movimiento,id_tipo_movimiento',
            'permisos.*.puede_ejecutar' => 'boolean',
            'permisos.*.requiere_autorizacion' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $nivel = NivelAutorizacion::find($idNivel);
        
        if (!$nivel) {
            return response()->json([
                'success' => false,
                'message' => 'Nivel de autorización no encontrado'
            ], 404);
        }

        foreach ($request->permisos as $permiso) {
            PermisoTipoMovimiento::updateOrCreate(
                [
                    'id_nivel_autorizacion' => $idNivel,
                    'id_tipo_movimiento' => $permiso['id_tipo_movimiento']
                ],
                [
                    'puede_ejecutar' => $permiso['puede_ejecutar'] ?? false,
                    'requiere_autorizacion' => $permiso['requiere_autorizacion'] ?? false,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Permisos de tipos de movimiento actualizados exitosamente'
        ]);
    }

    /**
     * Obtener permisos del usuario actual
     */
    public function getMisPermisos(Request $request)
    {
        $user = $request->user();
        
        $permisosSecciones = PermisoSeccion::with('seccion')
            ->where('id_nivel_autorizacion', $user->id_nivel_autorizacion)
            ->get();

        $permisosTiposMovimiento = PermisoTipoMovimiento::with('tipoMovimiento')
            ->where('id_nivel_autorizacion', $user->id_nivel_autorizacion)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'usuario' => $user->load('nivelAutorizacion'),
                'permisos_secciones' => $permisosSecciones,
                'permisos_tipos_movimiento' => $permisosTiposMovimiento
            ]
        ]);
    }

    /**
     * Verificar si el usuario actual tiene un permiso específico
     */
    public function verificarPermiso(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_seccion' => 'required|string',
            'accion' => 'required|in:leer,crear,modificar,eliminar',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $permiso = PermisoSeccion::whereHas('seccion', function($query) use ($request) {
            $query->where('codigo_seccion', $request->codigo_seccion);
        })
        ->where('id_nivel_autorizacion', $user->id_nivel_autorizacion)
        ->first();

        $tienePermiso = false;
        if ($permiso) {
            switch ($request->accion) {
                case 'leer':
                    $tienePermiso = $permiso->puede_leer;
                    break;
                case 'crear':
                    $tienePermiso = $permiso->puede_crear;
                    break;
                case 'modificar':
                    $tienePermiso = $permiso->puede_modificar;
                    break;
                case 'eliminar':
                    $tienePermiso = $permiso->puede_eliminar;
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tiene_permiso' => $tienePermiso,
                'seccion' => $request->codigo_seccion,
                'accion' => $request->accion
            ]
        ]);
    }
}