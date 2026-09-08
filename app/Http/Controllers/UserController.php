<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Services\AuditLogger;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string|unique:users,id',
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'shift' => 'required|integer|in:1,2,3',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;

        User::create($data);
        AuditLogger::log(auth()->id(), 'users', 'CREATE', "Menambah User baru: {$data['name']} (@{$data['username']}) dengan Role {$data['role_id']}");

        return back()->with('success', "User {$data['name']} berhasil ditambahkan.");
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'role_id' => 'required|exists:roles,id',
            'shift' => 'required|integer|in:1,2,3',
            'is_active' => 'required|boolean',
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        AuditLogger::log(auth()->id(), 'users', 'UPDATE', "Mengubah data User: {$user->name} (@{$user->username})");

        return back()->with('success', "Data User {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', "Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.");
        }

        $userName = $user->name;
        $user->delete();
        AuditLogger::log(auth()->id(), 'users', 'DELETE', "Menghapus User: {$userName}");

        return back()->with('success', "User {$userName} berhasil dihapus.");
    }
}
