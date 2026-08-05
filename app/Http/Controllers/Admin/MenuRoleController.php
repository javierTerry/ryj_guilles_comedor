<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MenuRoleController extends Controller
{
    /**
     * Redirección por defecto al primer submenú ("Visibilidad de Menús").
     */
    public function index()
    {
        return redirect()->route('admin.menu-roles.menus');
    }

    /**
     * Submenú 1: Asignación de Visibilidad de Menús y Submenús por Rol.
     */
    public function menus()
    {
        $roles = Role::orderBy('id', 'asc')->get();
        
        // Obtener todos los menús raíz con sus submenús y relaciones de rol
        $menus = Menu::whereNull('parent_id')
            ->with(['submenus' => function ($q) {
                $q->orderBy('order', 'asc')->with('roles');
            }, 'roles'])
            ->orderBy('order', 'asc')
            ->get();

        return view('admin.menu_roles.menus', compact('roles', 'menus'));
    }

    /**
     * Actualiza la asignación de roles para cada menú y submenú.
     */
    public function updateMenuRoles(Request $request)
    {
        $request->validate([
            'permissions' => 'nullable|array',
        ]);

        $permissions = $request->input('permissions', []);
        $allMenus = Menu::all();
        $adminUser = Auth::user();

        foreach ($allMenus as $menu) {
            // El Super Admin (Rol 1) siempre debe estar asignado a todos los menús
            $assignedRoleIds = [Role::SUPER_ADMIN];

            if (isset($permissions[$menu->id]) && is_array($permissions[$menu->id])) {
                foreach ($permissions[$menu->id] as $rId) {
                    $rIdInt = (int) $rId;
                    if ($rIdInt !== Role::SUPER_ADMIN && !in_array($rIdInt, $assignedRoleIds, true)) {
                        $assignedRoleIds[] = $rIdInt;
                    }
                }
            }

            $menu->roles()->sync($assignedRoleIds);
        }

        Log::channel('roles')->info('Actualización masiva de permisos de menús por el Super Admin', [
            'super_admin_id' => $adminUser->id,
            'super_admin_email' => $adminUser->email,
            'updated_menus_count' => $allMenus->count(),
            'ip' => $request->ip(),
        ]);

        return redirect()->route('admin.menu-roles.menus')
            ->with('status', 'Permisos de visibilidad de menús y submenús actualizados correctamente.');
    }

    /**
     * Submenú 2: Asignación de Roles a Usuarios Registrados con Paginación.
     */
    public function users(Request $request)
    {
        $roles = Role::orderBy('id', 'asc')->get();

        // Determinar cantidad de elementos por página (default 15, opciones: 15, 25, 50, 100)
        $perPage = (int) $request->input('per_page', 15);
        if (!in_array($perPage, [15, 25, 50, 100], true)) {
            $perPage = 15;
        }

        $users = User::with('role')
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        return view('admin.menu_roles.users', compact('roles', 'users', 'perPage'));
    }

    /**
     * Asigna un nuevo rol a un usuario específico.
     */
    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $oldRole = $user->role ? $user->role->nombre : 'Sin Rol';
        $newRoleModel = Role::findOrFail($request->role_id);
        $adminUser = Auth::user();

        $user->update([
            'role_id' => $request->role_id,
        ]);

        Log::channel('roles')->info('Cambio de rol de usuario realizado por Super Admin', [
            'super_admin_id' => $adminUser->id,
            'super_admin_email' => $adminUser->email,
            'target_user_id' => $user->id,
            'target_user_email' => $user->email,
            'previous_role' => $oldRole,
            'new_role' => $newRoleModel->nombre,
            'ip' => $request->ip(),
        ]);

        $perPage = $request->input('per_page', 15);

        return redirect()->route('admin.menu-roles.users', ['per_page' => $perPage, 'page' => $request->input('page', 1)])
            ->with('status', "El rol del usuario '{$user->name}' ha sido actualizado a '{$newRoleModel->nombre}'.");
    }
}
