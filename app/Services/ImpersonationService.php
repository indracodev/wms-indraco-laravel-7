<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\ImpersonationLog;
use Exception;

class ImpersonationService
{
    public function impersonate(User $targetUser)
    {
        $admin = Auth::user();

        if (!$admin || $admin->role_id !== 'super_admin') {
            throw new Exception("Hanya Super Admin yang dapat menggunakan fitur Login As (Impersonasi).");
        }

        if ($admin->id === $targetUser->id) {
            throw new Exception("Anda sudah berada dalam sesi akun ini.");
        }

        // Simpan Admin ID asli di Session jika belum impersonate
        if (!Session::has('impersonator_id')) {
            Session::put('impersonator_id', $admin->id);
        }

        // Catat Log Impersonasi
        ImpersonationLog::create([
            'admin_user_id' => Session::get('impersonator_id'),
            'target_user_id' => $targetUser->id,
            'started_at' => now(),
        ]);

        AuditLogger::log(Session::get('impersonator_id'), 'impersonate', 'IMPERSONATE', "Super Admin login sebagai user: {$targetUser->name} ({$targetUser->username})");

        // Switch Sesi Login ke Target User
        Auth::login($targetUser);
    }

    public function revert()
    {
        if (!Session::has('impersonator_id')) {
            return;
        }

        $adminId = Session::get('impersonator_id');
        $admin = User::findOrFail($adminId);

        // Update Log Impersonasi Terakhir
        ImpersonationLog::where('admin_user_id', $adminId)
            ->whereNull('ended_at')
            ->update(['ended_at' => now()]);

        AuditLogger::log($adminId, 'impersonate', 'REVERT', "Admin kembali ke sesi akun asli.");

        Session::forget('impersonator_id');
        Auth::login($admin);
    }
}
