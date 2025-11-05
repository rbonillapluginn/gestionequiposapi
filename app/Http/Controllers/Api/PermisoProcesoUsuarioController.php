<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PermisoProcesoUsuario;
use App\Models\User;
use App\Models\EstadoNota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PermisoProcesoUsuarioController extends Controller
{
    /**
     * Listar permisos de procesos por usuario
     * GET /api/permisos-procesos?id_usuario=1
     * GET /api/permisos-procesos?id_estado=2
     */
    public function index(Request $request)
    {
        $query = PermisoProcesoUsuario::with(['usuario', 'estado', 'usuarioAsigna']);

        // Filtro por usuario
        if ($request->filled('id_usuario')) {
            $query->where('id_usuario', $request->id_usuario);
        }

        // Filtro por estado/proceso
        if ($request->filled('id_estado')) {
            $query->where('id_estado', $request->id_estado);
        }

        // Filtro por permiso activo/inactivo
        if ($request->filled('tiene_permiso')) {
            $query->where('tiene_permiso', $request->tiene_permiso === 'true' || $request->tiene_permiso === '1');
        }

        $perPage = $request->get('per_page', 15);
        $permisos = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $permisos
        ]);
    }

    /**
     * Obtener todos los permisos de un usuario específico
     * GET /api/permisos-procesos/usuario/{id_usuario}
     */
    public function getPermisosPorUsuario($idUsuario)
    {
        $usuario = User::find($idUsuario);
        
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Obtener todos los estados
        $estados = EstadoNota::all();

        // Obtener permisos del usuario
        $permisosUsuario = PermisoProcesoUsuario::where('id_usuario', $idUsuario)->get()->keyBy('id_estado');

        // Construir respuesta con todos los estados y sus permisos
        $permisos = $estados->map(function($estado) use ($permisosUsuario) {
            $permiso = $permisosUsuario->get($estado->id_estado);
            
            return [
                'id_estado' => $estado->id_estado,
                'nombre_estado' => $estado->nombre_estado,
                'descripcion' => $estado->descripcion,
                'tiene_permiso' => $permiso ? $permiso->tiene_permiso : false,
                'fecha_asignacion' => $permiso ? $permiso->fecha_asignacion : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'usuario' => $usuario,
                'permisos' => $permisos
            ]
        ]);
    }

    /**
     * Asignar o actualizar permiso de proceso a usuario
     * POST /api/permisos-procesos
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_estado' => 'required|exists:estados_nota,id_estado',
            'tiene_permiso' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Buscar si ya existe el permiso
            $permiso = PermisoProcesoUsuario::where('id_usuario', $request->id_usuario)
                ->where('id_estado', $request->id_estado)
                ->first();

            if ($permiso) {
                // Actualizar permiso existente
                $permiso->update([
                    'tiene_permiso' => $request->tiene_permiso,
                    'id_usuario_asigna' => $request->user()->id_usuario,
                ]);

                $mensaje = 'Permiso actualizado exitosamente';
            } else {
                // Crear nuevo permiso
                $permiso = PermisoProcesoUsuario::create([
                    'id_usuario' => $request->id_usuario,
                    'id_estado' => $request->id_estado,
                    'tiene_permiso' => $request->tiene_permiso,
                    'id_usuario_asigna' => $request->user()->id_usuario,
                ]);

                $mensaje = 'Permiso asignado exitosamente';
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'data' => $permiso->load(['usuario', 'estado', 'usuarioAsigna'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar permiso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asignar múltiples permisos a un usuario
     * POST /api/permisos-procesos/asignar-multiple
     */
    public function asignarMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'permisos' => 'required|array|min:1',
            'permisos.*.id_estado' => 'required|exists:estados_nota,id_estado',
            'permisos.*.tiene_permiso' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $permisosCreados = [];

            foreach ($request->permisos as $permisoData) {
                $permiso = PermisoProcesoUsuario::updateOrCreate(
                    [
                        'id_usuario' => $request->id_usuario,
                        'id_estado' => $permisoData['id_estado']
                    ],
                    [
                        'tiene_permiso' => $permisoData['tiene_permiso'],
                        'id_usuario_asigna' => $request->user()->id_usuario,
                    ]
                );

                $permisosCreados[] = $permiso;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permisos asignados exitosamente',
                'data' => $permisosCreados
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar permisos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar permiso
     * DELETE /api/permisos-procesos/{id}
     */
    public function destroy($id)
    {
        $permiso = PermisoProcesoUsuario::find($id);

        if (!$permiso) {
            return response()->json([
                'success' => false,
                'message' => 'Permiso no encontrado'
            ], 404);
        }

        try {
            $permiso->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permiso eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar permiso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si usuario tiene permiso para un proceso
     * GET /api/permisos-procesos/verificar?id_usuario=1&id_estado=2
     */
    public function verificarPermiso(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_estado' => 'required|exists:estados_nota,id_estado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $tienePermiso = PermisoProcesoUsuario::tienePermiso(
            $request->id_usuario, 
            $request->id_estado
        );

        return response()->json([
            'success' => true,
            'data' => [
                'tiene_permiso' => $tienePermiso
            ]
        ]);
    }
}
