<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pallet;
use App\Models\PalletType;
use App\Models\Bin;
use App\Models\Box;
use App\Models\TransactionLog;
use App\Services\AuditLogger;
use Exception;
use DB;

class PalletController extends Controller
{
    /**
     * Display a listing of pallets with filter, stats, and search
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterBin = $request->input('bin_id');
        $filterType = $request->input('pallet_type_id');
        $filterStatus = $request->input('status');

        $query = Pallet::with([
            'bin.rack.warehouse',
            'type',
            'boxes' => function ($q) {
                $q->where('status', 'active')->with('product');
            }
        ]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('bin_id', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filterBin)) {
            $query->where('bin_id', $filterBin);
        }

        if (!empty($filterType)) {
            $query->where('pallet_type_id', $filterType);
        }

        if ($filterStatus === 'active') {
            $query->where('is_active', true)->whereHas('boxes', function ($q) {
                $q->where('status', 'active');
            });
        } elseif ($filterStatus === 'empty') {
            $query->where('is_active', true)->whereDoesntHave('boxes', function ($q) {
                $q->where('status', 'active');
            });
        } elseif ($filterStatus === 'inactive') {
            $query->where('is_active', false);
        }

        $pallets = $query->orderBy('id', 'asc')->paginate(15);

        // Fast SQL COUNT(*) Overview Statistics (Replaces heavy in-memory collection filtering)
        $stats = [
            'total' => Pallet::count(),
            'active_with_stock' => Pallet::where('is_active', true)->whereHas('boxes', function ($q) {
                $q->where('status', 'active');
            })->count(),
            'empty_transit' => Pallet::where('is_active', true)->whereDoesntHave('boxes', function ($q) {
                $q->where('status', 'active');
            })->count(),
            'inactive' => Pallet::where('is_active', false)->count(),
        ];

        // Optimized selective lookup queries
        $bins = Bin::select('id', 'rack_id')->orderBy('id', 'asc')->get();
        $palletTypes = PalletType::select('id', 'name')->orderBy('name', 'asc')->get();
        $products = \App\Models\Product::select('id', 'name', 'uom_inventory')->with('conversions')->orderBy('name', 'asc')->get();

        // AJAX / JSON SPA Response for zero page reloads
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $html = view('pallets.partials.table', compact(
                'pallets',
                'stats',
                'bins',
                'palletTypes',
                'products',
                'search',
                'filterBin',
                'filterType',
                'filterStatus'
            ))->render();

            return response()->json([
                'status' => 'success',
                'stats' => $stats,
                'html' => $html
            ]);
        }

        return view('pallets.index', compact(
            'pallets',
            'stats',
            'bins',
            'palletTypes',
            'products',
            'search',
            'filterBin',
            'filterType',
            'filterStatus'
        ));
    }

    /**
     * Register a new Pallet
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string|max:50|unique:pallets,id',
            'pallet_type_id' => 'required|exists:pallet_types,id',
            'bin_id' => 'nullable|exists:bins,id',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::transaction(function () use ($data, $request) {
                // If BIN is specified, check BIN capacity limit (max 2 active pallets per BIN)
                if (!empty($data['bin_id'])) {
                    $bin = Bin::findOrFail($data['bin_id']);
                    $currentPalletCount = Pallet::where('bin_id', $bin->id)
                                                ->where('is_active', true)
                                                ->count();
                    if ($currentPalletCount >= $bin->max_pallet) {
                        throw new Exception("Gagal: BIN {$bin->id} sudah penuh ({$currentPalletCount} Palet terdaftar). Maksimal {$bin->max_pallet} Palet per BIN.");
                    }
                }

                $pallet = Pallet::create([
                    'id' => strtoupper(trim($data['id'])),
                    'pallet_type_id' => $data['pallet_type_id'],
                    'bin_id' => $data['bin_id'] ?: null,
                    'is_active' => $request->has('is_active') ? (bool)$data['is_active'] : true,
                ]);

                AuditLogger::log(
                    auth()->id(),
                    'pallet',
                    'CREATE',
                    "Registrasi Palet Baru: Palet {$pallet->id} (Jenis: {$pallet->pallet_type_id}) di lokasi " . ($pallet->bin_id ?: 'TRANSIT')
                );
            });

            $msg = "Palet Baru '{$data['id']}' berhasil didaftarkan ke sistem!";

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'status' => 'success',
                    'message' => $msg
                ]);
            }

            return redirect()->route('pallets.index')->with('success', $msg);
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Get JSON Details of Pallet (Isi Box, Total Stok, Location & Tracking Logs)
     */
    public function show($id)
    {
        $pallet = Pallet::with([
            'bin.rack.warehouse',
            'type',
            'boxes' => function ($q) {
                $q->with('product');
            }
        ])->find($id);

        if (!$pallet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Palet tidak ditemukan'
            ], 404);
        }

        // Aggregate stock totals per product down to uom_base
        $stockTotals = [];
        $activeBoxes = [];
        foreach ($pallet->boxes as $box) {
            if ($box->status === 'active') {
                $pId = $box->product_id;
                $pName = $box->product->name ?? $box->product_id;
                $uom = $box->uom_base ?: ($box->product->uom_inventory ?? 'Box');

                if (!isset($stockTotals[$pId])) {
                    $stockTotals[$pId] = [
                        'product_id' => $pId,
                        'product_name' => $pName,
                        'total_qty' => 0,
                        'uom_base' => $uom,
                        'boxes_count' => 0
                    ];
                }
                $stockTotals[$pId]['total_qty'] += (float)$box->qty;
                $stockTotals[$pId]['boxes_count'] += 1;

                $activeBoxes[] = [
                    'id' => $box->id,
                    'product_id' => $pId,
                    'product_name' => $pName,
                    'qty' => (float)$box->qty,
                    'uom_base' => $uom,
                    'initial_qty' => (float)$box->initial_qty,
                    'expired_date' => $box->expired_date,
                    'machine_code' => $box->machine_code,
                    'status' => $box->status,
                ];
            }
        }

        // Fetch Tracking History Logs for this pallet
        $trackingLogs = TransactionLog::with('user')
                            ->where('pallet_id', $pallet->id)
                            ->orderBy('id', 'desc')
                            ->take(20)
                            ->get()
                            ->map(function ($log) {
                                return [
                                    'id' => $log->id,
                                    'date' => \Carbon\Carbon::parse($log->transaction_date)->format('d M Y H:i'),
                                    'pic' => $log->user->name ?? $log->pic_user_id,
                                    'status' => $log->status,
                                    'product_id' => $log->product_id,
                                    'qty_input' => (float)($log->qty_input ?? $log->qty),
                                    'uom_input' => $log->uom_input ?? $log->unit,
                                    'qty_base' => (float)($log->qty_base ?? $log->qty),
                                    'uom_base' => $log->uom_base,
                                    'bin_id' => $log->bin_id,
                                    'box_id' => $log->box_id,
                                    'notes' => $log->notes,
                                ];
                            });

        return response()->json([
            'status' => 'success',
            'pallet' => [
                'id' => $pallet->id,
                'type_name' => $pallet->type->name ?? $pallet->pallet_type_id,
                'bin_id' => $pallet->bin_id ?? 'TRANSIT',
                'warehouse_name' => $pallet->bin->rack->warehouse->name ?? 'Gudang Transit',
                'is_active' => (bool)$pallet->is_active,
                'total_boxes' => count($activeBoxes),
            ],
            'stock_totals' => array_values($stockTotals),
            'boxes' => $activeBoxes,
            'tracking_logs' => $trackingLogs
        ]);
    }

    /**
     * Update Pallet details (Type, Location BIN, Status)
     */
    public function update(Request $request, $id)
    {
        $pallet = Pallet::findOrFail($id);

        $data = $request->validate([
            'pallet_type_id' => 'required|exists:pallet_types,id',
            'bin_id' => 'nullable|exists:bins,id',
            'is_active' => 'required|boolean',
        ]);

        try {
            DB::transaction(function () use ($pallet, $data) {
                // If BIN location is changing, verify target BIN capacity
                if (!empty($data['bin_id']) && $data['bin_id'] !== $pallet->bin_id) {
                    $targetBin = Bin::findOrFail($data['bin_id']);
                    $currentPalletCount = Pallet::where('bin_id', $targetBin->id)
                                                ->where('is_active', true)
                                                ->where('id', '!=', $pallet->id)
                                                ->count();
                    if ($currentPalletCount >= $targetBin->max_pallet) {
                        throw new Exception("Gagal: BIN Tujuan {$targetBin->id} sudah penuh ({$currentPalletCount} Palet terdaftar). Maksimal {$targetBin->max_pallet} Palet per BIN.");
                    }
                }

                $oldBin = $pallet->bin_id;
                $pallet->update([
                    'pallet_type_id' => $data['pallet_type_id'],
                    'bin_id' => $data['bin_id'] ?: null,
                    'is_active' => (bool)$data['is_active'],
                ]);

                AuditLogger::log(
                    auth()->id(),
                    'pallet',
                    'UPDATE',
                    "Update Palet {$pallet->id}: Jenis={$data['pallet_type_id']}, Lokasi BIN=" . ($pallet->bin_id ?: 'TRANSIT') . " (Lama: {$oldBin}), Status=" . ($pallet->is_active ? 'Aktif' : 'Non-Aktif')
                );
            });

            $msg = "Informasi Palet '{$pallet->id}' berhasil diperbarui!";

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'status' => 'success',
                    'message' => $msg
                ]);
            }

            return redirect()->route('pallets.index')->with('success', $msg);
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete an empty Pallet
     */
    public function destroy(Request $request, $id)
    {
        $pallet = Pallet::findOrFail($id);

        // Verify pallet has no active boxes
        $activeBoxesCount = Box::where('pallet_id', $pallet->id)->where('status', 'active')->count();
        if ($activeBoxesCount > 0) {
            $errMsg = "Gagal Hapus: Palet '{$pallet->id}' masih terisi {$activeBoxesCount} Box barang aktif. Kosongkan stok terlebih dahulu!";
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'status' => 'error',
                    'message' => $errMsg
                ], 422);
            }
            return redirect()->back()->with('error', $errMsg);
        }

        try {
            $palletId = $pallet->id;
            $pallet->delete();

            AuditLogger::log(auth()->id(), 'pallet', 'DELETE', "Menghapus Palet Kosong '{$palletId}' dari sistem");

            $msg = "Palet Kosong '{$palletId}' berhasil dihapus.";

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'status' => 'success',
                    'message' => $msg
                ]);
            }

            return redirect()->route('pallets.index')->with('success', $msg);
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'status' => 'error',
                    'message' => "Gagal menghapus palet: " . $e->getMessage()
                ], 422);
            }
            return redirect()->back()->with('error', "Gagal menghapus palet: " . $e->getMessage());
        }
    }
}
