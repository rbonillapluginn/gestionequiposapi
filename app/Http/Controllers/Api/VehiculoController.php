<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehiculoController extends Controller
{
    /**
     * Listar vehículos
     */
    public function index(Request $request)
    {
        $query = Vehiculo::query();

        // Filtros
        $query->where('activo', true);


        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_camion', 'LIKE', "%{$search}%")
                  ->orWhere('placa', 'LIKE', "%{$search}%")
                  ->orWhere('modelo', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('numero_camion');

        $perPage = $request->get('per_page', 15);
        $vehiculos = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $vehiculos
        ]);
    }

    /**
     * Crear vehículo
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_camion' => 'required|string|max:50|unique:vehiculos,numero_camion',
            'placa' => 'nullable|string|max:20',
            'modelo' => 'nullable|string|max:100',
            'capacidad_carga' => 'nullable|numeric|min:0',
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
            $vehiculo = Vehiculo::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Vehículo creado exitosamente',
                'data' => $vehiculo
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear vehículo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar vehículo específico
     */
    public function show($id)
    {
        $vehiculo = Vehiculo::find($id);

        if (!$vehiculo) {
            return response()->json([
                'success' => false,
                'message' => 'Vehículo no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $vehiculo
        ]);
    }

    /**
     * Actualizar vehículo
     */
    public function update(Request $request, $id)
    {
        $vehiculo = Vehiculo::find($id);

        if (!$vehiculo) {
            return response()->json([
                'success' => false,
                'message' => 'Vehículo no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'numero_camion' => 'required|string|max:50|unique:vehiculos,numero_camion,' . $id . ',id_vehiculo',
            'placa' => 'nullable|string|max:20',
            'modelo' => 'nullable|string|max:100',
            'capacidad_carga' => 'nullable|numeric|min:0',
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
            $vehiculo->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Vehículo actualizado exitosamente',
                'data' => $vehiculo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar vehículo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar vehículo (soft delete cambiando activo a false)
     */
    public function destroy($id)
    {
        $vehiculo = Vehiculo::find($id);

        if (!$vehiculo) {
            return response()->json([
                'success' => false,
                'message' => 'Vehículo no encontrado'
            ], 404);
        }

        try {
            // Soft delete: cambiar activo a false
            $vehiculo->update(['activo' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Vehículo eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar vehículo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
