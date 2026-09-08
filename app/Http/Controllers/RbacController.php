<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RolePermission;
use App\Services\AuditLogger;

class RbacController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $modules = ['inbound', 'outbound', 'transfer', 'master', 'pallet', 'rbac', 'users', 'settings', 'logs', 'detail', 'locator'];

        return view('rbac.index', compact('roles', 'modules'));
    }

    public function update(Request $request)
    {
        $permissionsData = $request->input('permissions', []);

        foreach ($permissionsData as $roleId => $modules) {
            foreach ($modules as $module => $actions) {
                RolePermission::updateOrCreate(
                    ['role_id' => $roleId, 'module' => $module],
                    [
                        'can_read' => isset($actions['can_read']),
                        'can_create' => isset($actions['can_create']),
                        'can_update' => isset($actions['can_update']),
                        'can_delete' => isset($actions['can_delete']),
                    ]
                );
            }
        }

        AuditLogger::log(auth()->id(), 'rbac', 'UPDATE', 'Memperbarui Matriks Otorisasi Modul RBAC System');

        return back()->with('success', 'Matriks Permission Hak Akses berhasil diperbarui.');
    }
}
