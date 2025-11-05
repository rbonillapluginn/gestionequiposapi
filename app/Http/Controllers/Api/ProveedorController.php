<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{
    /**
     * Listar proveedores con paginación y filtros
     */
    public function index(Request $request)
    {
        $query = Proveedor::query();

        // Filtro por búsqueda (nombre, RUC, contacto)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_proveedor', 'LIKE', "%{$search}%")
                  ->orWhere('ruc', 'LIKE', "%{$search}%")
                  ->orWhere('contacto', 'LIKE', "%{$search}%");
            });
        }

       $query->where('estado', true);

        // Ordenar
        $query->orderBy('nombre_proveedor', 'asc');

        // Paginación
        $perPage = $request->get('per_page', 15);
        $proveedores = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Proveedores obtenidos exitosamente',
            'data' => $proveedores
        ]);
    }

    /**
     * Crear nuevo proveedor
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_proveedor' => 'required|string|max:150|unique:proveedores,nombre_proveedor',
            'ruc' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'contacto' => 'nullable|string|max:100',
            'estado' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $proveedor = Proveedor::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Proveedor creado exitosamente',
            'data' => $proveedor
        ], 201);
    }

    /**
     * Mostrar proveedor específico
     */
    public function show($id)
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $proveedor
        ]);
    }

    /**
     * Actualizar proveedor
     */
    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre_proveedor' => 'required|string|max:150|unique:proveedores,nombre_proveedor,' . $id . ',id_proveedor',
            'ruc' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'contacto' => 'nullable|string|max:100',
            'estado' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $proveedor->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Proveedor actualizado exitosamente',
            'data' => $proveedor
        ]);
    }

    /**
     * Eliminar (desactivar) proveedor
     */
    public function destroy($id)
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ], 404);
        }

        // Soft delete: cambiar estado a inactivo
        $proveedor->update(['estado' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Proveedor eliminado exitosamente'
        ]);
    }
}
