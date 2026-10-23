<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bin;
use App\Models\Rack;
use App\Models\Pallet;
use App\Models\PalletType;
use App\Models\Product;
use App\Models\ProductUomConversion;
use App\Models\Warehouse;
use App\Services\WarehouseTransactionService;
use Exception;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(WarehouseTransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    // --- API HELPER: DYNAMIC UOM CONVERSIONS ---
    public function getProductConversions($id)
    {
        $product = Product::with('conversions')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $conversions = [];
        // Base unit as factor 1
        $baseUnit = $product->uom_inventory ?: 'Box';
        $conversions[$baseUnit] = [
            'uom_convert' => $baseUnit,
            'qty_convert' => 1.0,
            'uom_base' => $baseUnit
        ];

        foreach ($product->conversions as $conv) {
            $conversions[$conv->uom_convert] = [
                'uom_convert' => $conv->uom_convert,
                'qty_convert' => (float) $conv->qty_convert,
                'uom_base' => $conv->uom_base ?: $baseUnit
            ];
        }

        return response()->json([
            'status' => 'success',
            'product_id' => $product->id,
            'name' => $product->name,
            'uom_inventory' => $baseUnit,
            'conversions' => $conversions
        ]);
    }

    // --- API HELPER: PALLET INFO & EXISTING PRODUCT / MULTI-ED CHECK ---
    public function getPalletInfo($id)
    {
        $pallet = Pallet::with(['activeBoxes.product.conversions', 'bin.rack'])->find($id);

        if (!$pallet) {
            return response()->json([
                'status' => 'success',
                'is_new' => true,
                'pallet_id' => $id,
                'pallet_status' => 'in_transit',
                'max_capacity' => 72,
                'total_qty' => 0,
                'active_product_id' => null,
                'active_product_name' => null,
                'boxes' => [],
            ]);
        }

        $activeBoxes = $pallet->activeBoxes;
        $activeProducts = $activeBoxes->pluck('product_id')->unique()->values();
        $primaryProduct = $activeBoxes->first() ? $activeBoxes->first()->product : null;

        return response()->json([
            'status' => 'success',
            'is_new' => false,
            'pallet_id' => $pallet->id,
            'pallet_type_id' => $pallet->pallet_type_id,
            'bin_id' => $pallet->bin_id,
            'pallet_status' => $pallet->status ?? ($pallet->bin_id ? 'on_rack' : 'in_transit'),
            'max_capacity' => $pallet->max_capacity ?? 72,
            'total_qty' => (float) $activeBoxes->sum('qty'),
            'active_product_id' => $activeProducts->first() ?? null,
            'active_product_name' => $primaryProduct ? "{$primaryProduct->name} ({$primaryProduct->id})" : null,
            'boxes' => $activeBoxes->map(function ($box) {
                return [
                    'id' => $box->id,
                    'product_id' => $box->product_id,
                    'product_name' => $box->product->name ?? $box->product_id,
                    'qty' => (float) $box->qty,
                    'uom_base' => $box->uom_base,
                    'expired_date' => $box->expired_date,
                    'machine_code' => $box->machine_code,
                    'notes' => $box->notes,
                ];
            }),
        ]);
    }

    // --- API HELPER: RACK / BIN INVENTORY FOR OUTBOUND PICKING ---
    public function getRackInventory($id)
    {
        // $id can be a BIN ID (e.g. B.10.1) or Rack ID (e.g. RK-B)
        $bins = Bin::where('id', $id)->orWhere('rack_id', $id)->pluck('id');

        $pallets = Pallet::with(['activeBoxes' => function ($q) {
            $q->orderBy('expired_date', 'asc')->with('product.conversions');
        }, 'bin.rack'])
        ->whereIn('bin_id', $bins)
        ->where('is_active', true)
        ->get();

        $items = [];
        foreach ($pallets as $plt) {
            foreach ($plt->activeBoxes as $box) {
                $items[] = [
                    'bin_id' => $plt->bin_id,
                    'rack_id' => $plt->bin && $plt->bin->rack ? $plt->bin->rack->id : '-',
                    'pallet_id' => $plt->id,
                    'pallet_capacity' => $plt->max_capacity ?? 72,
                    'pallet_status' => $plt->status,
                    'box_id' => $box->id,
                    'product_id' => $box->product_id,
                    'product_name' => $box->product->name ?? $box->product_id,
                    'brand' => $box->product->brand ?? '-',
                    'qty' => (float) $box->qty,
                    'uom_base' => $box->uom_base,
                    'expired_date' => $box->expired_date,
                    'machine_code' => $box->machine_code,
                    'notes' => $box->notes,
                    'conversions' => $box->product ? $box->product->conversions : [],
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'rack_or_bin_id' => $id,
            'items_count' => count($items),
            'items' => $items,
        ]);
    }

    // --- INBOUND ---
    public function inboundForm()
    {
        $bins = Bin::with('pallets')->get();
        $pallets = Pallet::with('activeBoxes.product')->where('is_active', true)->get();
        $palletTypes = PalletType::all();
        $products = Product::with('conversions')->get();
        $warehouses = Warehouse::all();
        $racks = Rack::with('bins')->get();

        return view('transactions.inbound', compact('bins', 'racks', 'pallets', 'palletTypes', 'products', 'warehouses'));
    }

    public function inbound(Request $request)
    {
        return $this->inboundV2($request);
    }

    public function inboundV2(Request $request)
    {
        $data = $request->validate([
            'pallet_id' => 'required|string|max:50',
            'max_capacity' => 'nullable|integer|in:18,72,84',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'expired_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'machine_code' => 'nullable|string|max:50',
            'shift' => 'required|integer|in:1,2,3',
            'bin_id' => 'nullable|string|max:50',
            'pallet_type_id' => 'nullable|string|max:50',
            'box_id' => 'nullable|string|max:50',
        ]);

        try {
            $data['max_capacity'] = $data['max_capacity'] ?? 72;
            $data['machine_code'] = $data['machine_code'] ?? 'MC-01';
            $box = $this->transactionService->processInboundV2($data, auth()->id());
            
            $statusText = (!empty($data['bin_id']) && $data['bin_id'] !== 'TRANSIT') 
                ? "ditempatkan pada Rak/BIN {$data['bin_id']}" 
                : "disimpan sementara (Status: IN TRANSIT)";

            return redirect()->route('transaction.inbound.form')->with('success', "Penerimaan Barang Masuk Sukses! Box {$box->id} ({$data['qty']} {$data['unit']}) berhasil dimasukkan ke Palet {$data['pallet_id']} dan {$statusText}.");
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function inboundPalletToBin(Request $request)
    {
        $data = $request->validate([
            'bin_id' => 'required|exists:bins,id',
            'pallet_id' => 'required|string|max:50',
        ]);

        try {
            $pallet = $this->transactionService->assignPalletToRack($data['pallet_id'], $data['bin_id'], auth()->id());
            return redirect()->route('transaction.inbound.form')->with('success', "Putaway Palet Sukses! Palet {$pallet->id} kini resmi ditempatkan pada BIN {$pallet->bin_id}.");
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function inboundBoxToPallet(Request $request)
    {
        return $this->inboundV2($request);
    }

    // --- OUTBOUND ---
    public function outboundForm()
    {
        $racks = Rack::with(['bins.pallets' => function ($q) {
            $q->where('is_active', true)->with('activeBoxes.product.conversions');
        }])->get();

        $bins = Bin::with(['pallets' => function ($q) {
            $q->where('is_active', true)->with('activeBoxes.product.conversions');
        }])->get();

        $products = Product::with('conversions')->get();

        return view('transactions.outbound', compact('racks', 'bins', 'products'));
    }

    public function outbound(Request $request)
    {
        return $this->outboundV2($request);
    }

    public function outboundV2(Request $request)
    {
        $data = $request->validate([
            'rack_id' => 'required|string|max:50',
            'pallet_id' => 'required|exists:pallets,id',
            'box_id' => 'required|exists:boxes,id',
            'qty' => 'required|numeric|min:0.01',
            'unit' => 'nullable|string|max:50',
            'shift' => 'required|integer|in:1,2,3',
            'outbound_type' => 'required|in:ekspedisi,in_transit',
        ]);

        try {
            $data['bin_id'] = $data['rack_id'];
            $this->transactionService->processOutboundV2($data, auth()->id());

            if ($data['outbound_type'] === 'ekspedisi') {
                $msg = "Pengeluaran Ekspedisi Sukses! {$data['qty']} {$data['unit']} telah dikeluarkan untuk pengiriman ekspedisi.";
            } else {
                $msg = "Pengeluaran In Transit Sukses! Palet {$data['pallet_id']} telah diturunkan dari rak ke status In Transit.";
            }

            return redirect()->route('transaction.outbound.form')->with('success', $msg);
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // --- TRANSFER ANTAR PALET (MIXED-PALLET TRANSFER) ---
    public function transferForm()
    {
        $pallets = Pallet::with(['bin', 'activeBoxes.product'])->where('is_active', true)->get();
        $bins = Bin::with('pallets')->get();

        return view('transactions.transfer', compact('pallets', 'bins'));
    }

    public function transfer(Request $request)
    {
        $data = $request->validate([
            'pallet_id' => 'required|exists:pallets,id',
            'target_bin_id' => 'required|exists:bins,id',
        ]);

        try {
            $pallet = $this->transactionService->assignPalletToRack($data['pallet_id'], $data['target_bin_id'], auth()->id());
            return redirect()->route('transaction.transfer.form')->with('success', "Relokasi Palet Sukses! Palet {$pallet->id} kini dipindahkan ke BIN {$pallet->bin_id}.");
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function transferPalletToPallet(Request $request)
    {
        $data = $request->validate([
            'source_pallet_id' => 'required|exists:pallets,id',
            'source_box_id' => 'required|exists:boxes,id',
            'target_pallet_id' => 'required|string|max:50',
            'qty' => 'required|numeric|min:0.01',
            'unit' => 'nullable|string|max:50',
        ]);

        try {
            $res = $this->transactionService->transferProductBetweenPallets($data, auth()->id());
            return redirect()->route('transaction.transfer.form')->with('success', "Transfer Antar Palet Sukses! {$data['qty']} {$data['unit']} berhasil dipindahkan dari Palet {$data['source_pallet_id']} ke Palet {$data['target_pallet_id']}.");
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
