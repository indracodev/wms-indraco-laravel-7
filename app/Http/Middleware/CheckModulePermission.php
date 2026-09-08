<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckModulePermission
{
    public function handle(Request $request, Closure $next, string $module, string $action = 'read')
    {
        $user = Auth::user();

        if (!$user || !$user->hasPermission($module, $action)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Akses Ditolak: Role Anda (" . ($user ? $user->role->name : 'Guest') . ") tidak memiliki izin {$action} pada modul {$module}!"
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', "Akses Ditolak: Role Anda (" . ($user ? $user->role->name : 'Guest') . ") tidak memiliki izin {$action} pada modul {$module}!");
        }

        return $next($request);
    }
}
