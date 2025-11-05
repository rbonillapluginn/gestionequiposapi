<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartamentoController extends Controller
{
    /**
     * Listar departamentos
     */
    public function index(Request $request)
    {
        $query = Departamento::query();

        $query->where('activo', true);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_departamento', 'LIKE', "%{$search}%")
                  ->orWhere('codigo_departamento', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $departamentos = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $departamentos
        ]);
    }

    /**
     * Crear departamento
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_departamento' => 'required|string|max:100|unique:departamentos',
            'codigo_departamento' => 'required|string|max:20|unique:departamentos',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $departamento = Departamento::create([
            'nombre_departamento' => $request->nombre_departamento,
            'codigo_departamento' => $request->codigo_departamento,
            'descripcion' => $request->descripcion,
            'activo' => $request->get('activo', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Departamento creado exitosamente',
            'data' => $departamento
        ], 201);
    }

    /**
     * Mostrar departamento específico
     */
    public function show($id)
    {
        $departamento = Departamento::find($id);

        if (!$departamento) {
            return response()->json([
                'success' => false,
                'message' => 'Departamento no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $departamento
        ]);
    }

    /**
     * Actualizar departamento
     */
    public function update(Request $request, $id)
    {
        $departamento = Departamento::find($id);

        if (!$departamento) {
            return response()->json([
                'success' => false,
                'message' => 'Departamento no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre_departamento' => 'sometimes|string|max:100|unique:departamentos,nombre_departamento,' . $id . ',id_departamento',
            'codigo_departamento' => 'sometimes|string|max:20|unique:departamentos,codigo_departamento,' . $id . ',id_departamento',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $departamento->update($request->only([
            'nombre_departamento', 'codigo_departamento', 'descripcion', 'activo'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Departamento actualizado exitosamente',
            'data' => $departamento
        ]);
    }

    /**
     * Eliminar departamento (soft delete)
     */
    public function destroy($id)
    {
        $departamento = Departamento::find($id);

        if (!$departamento) {
            return response()->json([
                'success' => false,
                'message' => 'Departamento no encontrado'
            ], 404);
        }

        $departamento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Departamento desactivado exitosamente'
        ]);
    }
}
