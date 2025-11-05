<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chofer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChoferController extends Controller
{
    /**
     * Listar choferes
     */
    public function index(Request $request)
    {
        $query = Chofer::query();

        // Filtros
        $query->where('activo', true);


        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_completo', 'LIKE', "%{$search}%")
                  ->orWhere('licencia', 'LIKE', "%{$search}%")
                  ->orWhere('telefono', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('nombre_completo');

        $perPage = $request->get('per_page', 15);
        $choferes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $choferes
        ]);
    }

    /**
     * Crear chofer
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_completo' => 'required|string|max:200',
            'licencia' => 'required|string|max:50|unique:choferes,licencia',
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
            $chofer = Chofer::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Chofer creado exitosamente',
                'data' => $chofer
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear chofer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar chofer específico
     */
    public function show($id)
    {
        $chofer = Chofer::find($id);

        if (!$chofer) {
            return response()->json([
                'success' => false,
                'message' => 'Chofer no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $chofer
        ]);
    }

    /**
     * Actualizar chofer
     */
    public function update(Request $request, $id)
    {
        $chofer = Chofer::find($id);

        if (!$chofer) {
            return response()->json([
                'success' => false,
                'message' => 'Chofer no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre_completo' => 'required|string|max:200',
            'licencia' => 'required|string|max:50|unique:choferes,licencia,' . $id . ',id_chofer',
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
            $chofer->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Chofer actualizado exitosamente',
                'data' => $chofer
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar chofer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar chofer (soft delete cambiando activo a false)
     */
    public function destroy($id)
    {
        $chofer = Chofer::find($id);

        if (!$chofer) {
            return response()->json([
                'success' => false,
                'message' => 'Chofer no encontrado'
            ], 404);
        }

        try {
            // Soft delete: cambiar activo a false
            $chofer->update(['activo' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Chofer eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar chofer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
