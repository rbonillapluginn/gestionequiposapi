<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mensajero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MensajeroController extends Controller
{
    /**
     * Listar mensajeros
     */
    public function index(Request $request)
    {
        $query = Mensajero::query();

        // Filtros
        $query->where('activo', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_completo', 'LIKE', "%{$search}%")
                  ->orWhere('identificacion', 'LIKE', "%{$search}%")
                  ->orWhere('telefono', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('nombre_completo');

        $perPage = $request->get('per_page', 15);
        $mensajeros = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $mensajeros
        ]);
    }

    /**
     * Crear mensajero
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_completo' => 'required|string|max:200',
            'identificacion' => 'required|string|max:50|unique:mensajeros,identificacion',
            'telefono' => 'nullable|string|max:20',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mensajero = Mensajero::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Mensajero creado exitosamente',
                'data' => $mensajero
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear mensajero',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar mensajero específico
     */
    public function show($id)
    {
        $mensajero = Mensajero::find($id);

        if (!$mensajero) {
            return response()->json([
                'success' => false,
                'message' => 'Mensajero no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $mensajero
        ]);
    }

    /**
     * Actualizar mensajero
     */
    public function update(Request $request, $id)
    {
        $mensajero = Mensajero::find($id);

        if (!$mensajero) {
            return response()->json([
                'success' => false,
                'message' => 'Mensajero no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre_completo' => 'required|string|max:200',
            'identificacion' => 'required|string|max:50|unique:mensajeros,identificacion,' . $id . ',id_mensajero',
            'telefono' => 'nullable|string|max:20',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mensajero->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Mensajero actualizado exitosamente',
                'data' => $mensajero
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar mensajero',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar mensajero (soft delete cambiando activo a false)
     */
    public function destroy($id)
    {
        $mensajero = Mensajero::find($id);

        if (!$mensajero) {
            return response()->json([
                'success' => false,
                'message' => 'Mensajero no encontrado'
            ], 404);
        }

        try {
            // Soft delete: cambiar activo a false
            $mensajero->update(['activo' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Mensajero eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar mensajero',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
