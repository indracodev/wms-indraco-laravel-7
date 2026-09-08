<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\AuditLog;
use App\Models\TransactionLog;
use App\Services\AuditLogger;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userAudits = AuditLog::where('user_id', $user->id)->latest()->take(10)->get();
        $userTransactions = TransactionLog::where('pic_user_id', $user->id)->latest()->take(10)->get();

        return view('profile.index', compact('user', 'userAudits', 'userTransactions'));
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->with('error', 'Password lama yang Anda masukkan tidak cocok.');
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        AuditLogger::log($user->id, 'profile', 'UPDATE', 'Mengubah password akun pribadi.');

        return back()->with('success', 'Password Anda berhasil diperbarui.');
    }
}
