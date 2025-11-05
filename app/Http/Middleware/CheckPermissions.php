<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PermisoSeccion;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $seccion, string $accion = 'leer'): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        if (!$user->activo) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario inactivo'
            ], 403);
        }

        // Verificar permisos
        $permiso = PermisoSeccion::whereHas('seccion', function($query) use ($seccion) {
            $query->where('codigo_seccion', $seccion);
        })
        ->where('id_nivel_autorizacion', $user->id_nivel_autorizacion)
        ->first();

        if (!$permiso) {
            return response()->json([
                'success' => false,
                'message' => 'Sin permisos para acceder a esta sección'
            ], 403);
        }

        // Verificar acción específica
        $tienePermiso = false;
        switch ($accion) {
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

        if (!$tienePermiso) {
            return response()->json([
                'success' => false,
                'message' => "Sin permisos para {$accion} en esta sección"
            ], 403);
        }

        return $next($request);
    }
}