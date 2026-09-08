<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\ImpersonationService;
use Exception;

class ImpersonationController extends Controller
{
    protected $impersonationService;

    public function __construct(ImpersonationService $impersonationService)
    {
        $this->impersonationService = $impersonationService;
    }

    public function start(User $user)
    {
        try {
            $this->impersonationService->impersonate($user);
            return redirect()->route('dashboard')->with('success', "Sesi berhasil dialihkan! Anda saat ini bertindak sebagai: {$user->name} ({$user->role->name}).");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function revert()
    {
        try {
            $this->impersonationService->revert();
            return redirect()->route('dashboard')->with('success', "Sesi Admin asli Anda berhasil dipulihkan!");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
