<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticuloController extends Controller
{
    /**
     * Listar artículos
     */
    public function index(Request $request)
    {
        $query = Articulo::with('categoria');

        // Filtros
        $query->where('activo', true);

        if ($request->has('id_categoria')) {
            $query->where('id_categoria', $request->id_categoria);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_articulo', 'LIKE', "%{$search}%")
                  ->orWhere('codigo_barra', 'LIKE', "%{$search}%")
                  ->orWhere('numero_serie', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $articulos = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $articulos
        ]);
    }

    /**
     * Crear artículo
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_articulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'id_categoria' => 'nullable|exists:categorias_articulos,id_categoria',
            'codigo_barra' => 'nullable|string|max:100|unique:articulos',
            'numero_serie' => 'nullable|string|max:100|unique:articulos',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'estado' => 'nullable|in:disponible,en_uso,en_reparacion,dado_de_baja',
            'observaciones' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'activo' => 'boolean',
        ]);

        // Validación personalizada: código de barra o número de serie requerido
        $validator->after(function ($validator) use ($request) {
            if (!$request->codigo_barra && !$request->numero_serie) {
                $validator->errors()->add('identificacion', 'Debe proporcionar código de barra o número de serie');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Obtener el ID de la categoría por nombre si se envió como texto
        $idCategoria = $request->id_categoria;
        if ($request->has('categoria') && !is_numeric($request->categoria)) {
            $categoria = \App\Models\CategoriaArticulo::where('nombre_categoria', $request->categoria)->first();
            $idCategoria = $categoria ? $categoria->id_categoria : null;
        }

        $articulo = Articulo::create([
            'nombre_articulo' => $request->nombre_articulo,
            'descripcion' => $request->descripcion,
            'id_categoria' => $idCategoria,
            'codigo_barra' => $request->codigo_barra,
            'numero_serie' => $request->numero_serie,
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'estado' => $request->get('estado', 'disponible'),
            'observaciones' => $request->observaciones,
            'precio' => $request->precio,
            'activo' => $request->get('activo', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Artículo creado exitosamente',
            'data' => $articulo->load('categoria')
        ], 201);
    }

    /**
     * Mostrar artículo específico
     */
    public function show($id)
    {
        $articulo = Articulo::with('categoria')->find($id);

        if (!$articulo) {
            return response()->json([
                'success' => false,
                'message' => 'Artículo no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $articulo
        ]);
    }

    /**
     * Actualizar artículo
     */
    public function update(Request $request, $id)
    {
        $articulo = Articulo::find($id);

        if (!$articulo) {
            return response()->json([
                'success' => false,
                'message' => 'Artículo no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre_articulo' => 'sometimes|string|max:200',
            'descripcion' => 'nullable|string',
            'id_categoria' => 'nullable|exists:categorias_articulos,id_categoria',
            'codigo_barra' => 'nullable|string|max:100|unique:articulos,codigo_barra,' . $id . ',id_articulo',
            'numero_serie' => 'nullable|string|max:100|unique:articulos,numero_serie,' . $id . ',id_articulo',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'estado' => 'nullable|in:disponible,en_uso,en_reparacion,dado_de_baja',
            'observaciones' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Obtener el ID de la categoría por nombre si se envió como texto
        $dataToUpdate = $request->only([
            'nombre_articulo', 'descripcion', 'id_categoria', 'codigo_barra', 
            'numero_serie', 'marca', 'modelo', 'estado', 'observaciones', 'precio', 'activo'
        ]);

        if ($request->has('categoria') && !is_numeric($request->categoria)) {
            $categoria = \App\Models\CategoriaArticulo::where('nombre_categoria', $request->categoria)->first();
            $dataToUpdate['id_categoria'] = $categoria ? $categoria->id_categoria : null;
        }

        $articulo->update($dataToUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Artículo actualizado exitosamente',
            'data' => $articulo->load('categoria')
        ]);
    }

    /**
     * Eliminar artículo (soft delete)
     */
    public function destroy($id)
    {
        $articulo = Articulo::find($id);

        if (!$articulo) {
            return response()->json([
                'success' => false,
                'message' => 'Artículo no encontrado'
            ], 404);
        }

        $articulo->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Artículo desactivado exitosamente'
        ]);
    }

    /**
     * Buscar artículo por código de barra o número de serie
     */
    public function buscarPorCodigo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $codigo = trim($request->codigo);
        
        $articulo = Articulo::with('categoria')
            ->where(function($query) use ($codigo) {
                $query->where('codigo_barra', $codigo)
                      ->orWhere('numero_serie', $codigo);
            })
            ->where('activo', true)
            ->first();

        if (!$articulo) {
            return response()->json([
                'success' => false,
                'message' => 'Artículo no encontrado con el código proporcionado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $articulo
        ]);
    }
}