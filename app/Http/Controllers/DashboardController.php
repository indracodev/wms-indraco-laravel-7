<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse;
use App\Models\Bin;
use App\Models\Pallet;
use App\Models\Product;
use App\Models\Box;
use App\Models\TransactionLog;
use App\Models\AuditLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        // Common Data
        $totalWarehouses = Warehouse::count();
        $totalBins = Bin::count();
        $occupiedBins = Bin::has('pallets')->count();
        $occupancyRate = $totalBins > 0 ? round(($occupiedBins / $totalBins) * 100, 1) : 0;
        $totalStockQty = Box::sum('qty');
        $totalPallets = Pallet::where('is_active', true)->count();

        // Expiring Soon (under 90 days)
        $expiringBoxes = Box::with(['product', 'pallet.bin'])
                            ->where('expired_date', '<=', Carbon::now()->addDays(90))
                            ->orderBy('expired_date', 'asc')
                            ->take(10)
                            ->get();

        // Critical Bins (Bins with 2 pallets - 100% full capacity)
        $fullBins = Bin::with(['rack.warehouse', 'pallets.boxes.product'])
                       ->whereHas('pallets', function ($q) {
                           $q->where('is_active', true);
                       }, '>=', 2)
                       ->get();

        // Recent Logs
        $recentTransactions = TransactionLog::with(['user', 'product', 'bin'])
                                            ->latest()
                                            ->take(10)
                                            ->get();

        $recentAudits = AuditLog::with('user')->latest()->take(10)->get();

        return view('dashboard.index', compact(
            'user', 'roleId', 'totalWarehouses', 'totalBins', 'occupiedBins',
            'occupancyRate', 'totalStockQty', 'totalPallets', 'expiringBoxes',
            'fullBins', 'recentTransactions', 'recentAudits'
        ));
    }
}
