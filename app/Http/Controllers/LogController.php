<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionLog;
use App\Models\AuditLog;
use App\Models\ImpersonationLog;
use App\Models\Warehouse;
use App\Models\Rack;
use App\Models\Bin;
use App\Models\Pallet;
use App\Models\Product;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'transaction');
        $sortDir = in_array(strtolower($request->get('dir', 'desc')), ['asc', 'desc']) ? strtolower($request->get('dir', 'desc')) : 'desc';

        // 1. Transaction Logs
        $txQuery = TransactionLog::with(['user', 'product', 'bin.rack', 'pallet', 'warehouse']);

        if ($request->filled('tx_search')) {
            $s = $request->tx_search;
            $txQuery->where(function($q) use ($s) {
                $q->where('product_id', 'like', "%{$s}%")
                  ->orWhere('pic_user_id', 'like', "%{$s}%")
                  ->orWhere('bin_id', 'like', "%{$s}%")
                  ->orWhere('pallet_id', 'like', "%{$s}%")
                  ->orWhere('warehouse_id', 'like', "%{$s}%")
                  ->orWhereHas('user', function($u) use ($s) {
                      $u->where('name', 'like', "%{$s}%");
                  })
                  ->orWhereHas('product', function($p) use ($s) {
                      $p->where('name', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('tx_status')) {
            $txQuery->where('status', $request->tx_status);
        }

        if ($request->filled('tx_wh')) {
            $txQuery->where('warehouse_id', $request->tx_wh);
        }

        if ($request->filled('tx_product')) {
            $txQuery->where('product_id', $request->tx_product);
        }

        if ($request->filled('tx_rack')) {
            $r = $request->tx_rack;
            $txQuery->whereHas('bin', function($q) use ($r) {
                $q->where('rack_id', $r);
            });
        }

        if ($request->filled('tx_bin')) {
            $txQuery->where('bin_id', $request->tx_bin);
        }

        if ($request->filled('tx_pallet')) {
            $txQuery->where('pallet_id', $request->tx_pallet);
        }

        if ($request->filled('tx_date_from')) {
            $txQuery->whereDate('transaction_date', '>=', $request->tx_date_from);
        }

        if ($request->filled('tx_date_to')) {
            $txQuery->whereDate('transaction_date', '<=', $request->tx_date_to);
        }

        $txSortBy = $request->get('tx_sort', 'transaction_date');
        $transactions = $txQuery->orderBy($txSortBy, $sortDir)
                                ->paginate(20, ['*'], 'tx_page')
                                ->withQueryString();

        // 2. Audit Logs
        $auditQuery = AuditLog::with('user');

        if ($request->filled('audit_search')) {
            $s = $request->audit_search;
            $auditQuery->where(function($q) use ($s) {
                $q->where('module', 'like', "%{$s}%")
                  ->orWhere('action_type', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('user_id', 'like', "%{$s}%")
                  ->orWhereHas('user', function($u) use ($s) {
                      $u->where('name', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('audit_module')) {
            $auditQuery->where('module', $request->audit_module);
        }

        if ($request->filled('audit_action')) {
            $auditQuery->where('action_type', $request->audit_action);
        }

        $auditSortBy = $request->get('audit_sort', 'timestamp');
        $audits = $auditQuery->orderBy($auditSortBy, $sortDir)
                             ->paginate(20, ['*'], 'audit_page')
                             ->withQueryString();

        // 3. Impersonation Logs
        $impQuery = ImpersonationLog::with(['admin', 'target']);

        if ($request->filled('imp_search')) {
            $s = $request->imp_search;
            $impQuery->where(function($q) use ($s) {
                $q->where('admin_user_id', 'like', "%{$s}%")
                  ->orWhere('target_user_id', 'like', "%{$s}%")
                  ->orWhereHas('admin', function($u) use ($s) {
                      $u->where('name', 'like', "%{$s}%");
                  })
                  ->orWhereHas('target', function($u) use ($s) {
                      $u->where('name', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('imp_status')) {
            if ($request->imp_status === 'active') {
                $impQuery->whereNull('ended_at');
            } elseif ($request->imp_status === 'ended') {
                $impQuery->whereNotNull('ended_at');
            }
        }

        $impSortBy = $request->get('imp_sort', 'started_at');
        $impersonations = $impQuery->orderBy($impSortBy, $sortDir)
                                   ->paginate(20, ['*'], 'imp_page')
                                   ->withQueryString();

        // Filter Lookup data
        $warehouses = Warehouse::orderBy('name', 'asc')->get();
        $racks = Rack::orderBy('id', 'asc')->get();
        $bins = Bin::orderBy('id', 'asc')->get();
        $pallets = Pallet::orderBy('id', 'asc')->get();
        $products = Product::orderBy('name', 'asc')->get();

        $auditModules = AuditLog::whereNotNull('module')->distinct()->pluck('module')->sort()->values();
        $auditActions = AuditLog::whereNotNull('action_type')->distinct()->pluck('action_type')->sort()->values();

        return view('logs.index', compact(
            'transactions', 'audits', 'impersonations', 'activeTab',
            'warehouses', 'racks', 'bins', 'pallets', 'products',
            'auditModules', 'auditActions', 'sortDir'
        ));
    }
}
