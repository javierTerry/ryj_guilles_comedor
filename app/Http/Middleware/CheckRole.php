<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // El Super Admin (Rol 1) siempre tiene permiso de acceso
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Convertir parámetros de roles a un array de enteros/strings
        $allowedRoles = array_map(fn($r) => is_numeric($r) ? (int)$r : $r, $roles);

        $hasAccess = in_array((int) $user->role_id, $allowedRoles, true) ||
                     in_array(optional($user->role)->slug, $allowedRoles, true);

        if (!$hasAccess) {
            Log::channel('roles')->warning('Acceso denegado a ruta por restricción de rol', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'role_id' => $user->role_id,
                'requested_url' => $request->fullUrl(),
                'required_roles' => $allowedRoles,
                'ip' => $request->ip(),
            ]);

            abort(403, 'No tienes permisos suficientes para acceder a este recurso.');
        }

        return $next($request);
    }
}
