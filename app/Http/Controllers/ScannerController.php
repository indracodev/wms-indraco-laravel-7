<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pallet;
use App\Models\Product;
use App\Models\Bin;
use App\Models\Box;

class ScannerController extends Controller
{
    public function index()
    {
        return view('scanner.index');
    }

    public function scan(Request $request)
    {
        $code = trim($request->input('code'));

        if (empty($code)) {
            return response()->json(['status' => 'error', 'message' => 'Kode Barcode tidak boleh kosong.'], 400);
        }

        // Pattern matching
        // 1. Pallet: PLT-
        if (strpos($code, 'PLT-') === 0 || strpos($code, 'PLT') === 0) {
            $pallet = Pallet::with(['bin', 'type', 'boxes.product'])->find($code);
            if ($pallet) {
                return response()->json([
                    'status' => 'success',
                    'type' => 'pallet',
                    'data' => [
                        'id' => $pallet->id,
                        'type_name' => $pallet->type->name ?? $pallet->pallet_type_id,
                        'bin_id' => $pallet->bin_id ?? 'Transit / Belum di BIN',
                        'is_active' => $pallet->is_active,
                        'boxes' => $pallet->boxes->map(function ($box) {
                            return [
                                'box_id' => $box->id,
                                'product_name' => $box->product->name ?? $box->product_id,
                                'qty' => $box->qty,
                                'expired_date' => $box->expired_date,
                                'machine_code' => $box->machine_code,
                            ];
                        })
                    ]
                ]);
            }
        }

        // 2. Product SKU: PRD-
        if (strpos($code, 'PRD-') === 0 || strpos($code, 'PRD') === 0) {
            $product = Product::with(['boxes.pallet.bin'])->find($code);
            if ($product) {
                $totalStock = $product->boxes->sum('qty');
                return response()->json([
                    'status' => 'success',
                    'type' => 'product',
                    'data' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'unit' => $product->default_unit,
                        'total_stock' => $totalStock,
                        'locations' => $product->boxes->map(function ($box) {
                            return [
                                'box_id' => $box->id,
                                'pallet_id' => $box->pallet_id,
                                'bin_id' => $box->pallet->bin_id ?? 'Transit',
                                'qty' => $box->qty,
                                'expired_date' => $box->expired_date,
                            ];
                        })
                    ]
                ]);
            }
        }

        // 3. BIN Code pattern e.g., B.10.2, C.1.1
        $bin = Bin::with(['rack.warehouse', 'pallets.boxes.product'])->find($code);
        if ($bin) {
            return response()->json([
                'status' => 'success',
                'type' => 'bin',
                'data' => [
                    'id' => $bin->id,
                    'warehouse' => $bin->rack->warehouse->name ?? '',
                    'max_pallet' => $bin->max_pallet,
                    'current_pallets_count' => $bin->pallets->where('is_active', true)->count(),
                    'is_full' => $bin->isFull(),
                    'pallets' => $bin->pallets->where('is_active', true)->values()->map(function ($plt) {
                        return [
                            'pallet_id' => $plt->id,
                            'type' => $plt->type->name ?? $plt->pallet_type_id,
                            'boxes_count' => $plt->boxes->count(),
                            'total_qty' => $plt->boxes->sum('qty'),
                        ];
                    })
                ]
            ]);
        }

        // Fallback Box search
        $box = Box::with(['pallet.bin', 'product'])->find($code);
        if ($box) {
            return response()->json([
                'status' => 'success',
                'type' => 'box',
                'data' => [
                    'id' => $box->id,
                    'product_name' => $box->product->name ?? $box->product_id,
                    'pallet_id' => $box->pallet_id,
                    'bin_id' => $box->pallet->bin_id ?? 'Transit',
                    'qty' => $box->qty,
                    'expired_date' => $box->expired_date,
                    'machine_code' => $box->machine_code,
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => "Barcode [{$code}] tidak ditemukan dalam database sistem WMS Indraco."
        ], 404);
    }
}
