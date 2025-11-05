<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tienda;
use App\Models\EncargadoTienda;
use App\Models\Departamento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TiendaController extends Controller
{
    /**
     * Listar tiendas
     */
    public function index(Request $request)
    {
        $query = Tienda::with('encargados.usuario', 'encargados.departamento');

        $query->where('activo', true);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_tienda', 'LIKE', "%{$search}%")
                  ->orWhere('codigo_tienda', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $tiendas = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tiendas
        ]);
    }

    /**
     * Mostrar tienda específica
     */
    public function show($id)
    {
        $tienda = Tienda::with('encargados.usuario', 'encargados.departamento')->find($id);

        if (!$tienda) {
            return response()->json([
                'success' => false,
                'message' => 'Tienda no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tienda
        ]);
    }

    /**
     * Obtener encargados de una tienda
     */
    public function getEncargados($idTienda)
    {
        $tienda = Tienda::find($idTienda);

        if (!$tienda) {
            return response()->json([
                'success' => false,
                'message' => 'Tienda no encontrada'
            ], 404);
        }

        // Obtener encargados actuales con sus relaciones
        $encargados = EncargadoTienda::with(['usuario', 'departamento'])
            ->where('id_tienda', $idTienda)
            ->where('activo', true)
            ->get();

        // Obtener departamentos ya asignados
        $departamentosAsignados = $encargados->pluck('id_departamento')->toArray();

        // Obtener departamentos disponibles (no asignados)
        $departamentosDisponibles = Departamento::where('activo', true)
            ->whereNotIn('id_departamento', $departamentosAsignados)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'tienda' => [
                    'id_tienda' => $tienda->id_tienda,
                    'nombre_tienda' => $tienda->nombre_tienda,
                    'codigo_tienda' => $tienda->codigo_tienda
                ],
                'encargados' => $encargados,
                'departamentos_disponibles' => $departamentosDisponibles
            ]
        ]);
    }

    /**
     * Asignar encargado a departamento de tienda
     */
    public function storeEncargado(Request $request, $idTienda)
    {
        $tienda = Tienda::find($idTienda);

        if (!$tienda) {
            return response()->json([
                'success' => false,
                'message' => 'Tienda no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_departamento' => 'required|exists:departamentos,id_departamento',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar que el usuario esté activo
        $usuario = User::find($request->id_usuario);
        if (!$usuario || !$usuario->activo) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no está activo o no existe'
            ], 422);
        }

        // Verificar que el departamento no esté ya asignado en esta tienda
        $existeEncargado = EncargadoTienda::where('id_tienda', $idTienda)
            ->where('id_departamento', $request->id_departamento)
            ->where('activo', true)
            ->exists();

        if ($existeEncargado) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => [
                    'id_departamento' => ['Ya existe un encargado para este departamento en esta tienda']
                ]
            ], 422);
        }

        // Crear el encargado
        $encargado = EncargadoTienda::create([
            'id_tienda' => $idTienda,
            'id_usuario' => $request->id_usuario,
            'id_departamento' => $request->id_departamento,
            'fecha_asignacion' => now()->format('Y-m-d'),
            'activo' => true,
        ]);

        // Cargar relaciones
        $encargado->load(['usuario', 'departamento']);

        return response()->json([
            'success' => true,
            'message' => 'Encargado asignado exitosamente',
            'data' => $encargado
        ], 201);
    }

    /**
     * Remover encargado de tienda
     */
    public function destroyEncargado($idTienda, $idEncargado)
    {
        $tienda = Tienda::find($idTienda);

        if (!$tienda) {
            return response()->json([
                'success' => false,
                'message' => 'Tienda no encontrada'
            ], 404);
        }

        $encargado = EncargadoTienda::where('id_encargado', $idEncargado)
            ->where('id_tienda', $idTienda)
            ->first();

        if (!$encargado) {
            return response()->json([
                'success' => false,
                'message' => 'Encargado no encontrado'
            ], 404);
        }

        // Soft delete: marcar como inactivo
        $encargado->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Encargado removido exitosamente'
        ]);
    }
}