<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bin;
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

    // --- API HELPER FOR DYNAMIC UOM CONVERSIONS ---
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
        // Include base unit as 1 factor
        $conversions[$product->uom_inventory] = [
            'uom_convert' => $product->uom_inventory,
            'qty_convert' => 1.0,
            'uom_base' => $product->uom_inventory
        ];

        foreach ($product->conversions as $conv) {
            $conversions[$conv->uom_convert] = [
                'uom_convert' => $conv->uom_convert,
                'qty_convert' => (float) $conv->qty_convert,
                'uom_base' => $conv->uom_base ?: $product->uom_inventory
            ];
        }

        return response()->json([
            'status' => 'success',
            'product_id' => $product->id,
            'name' => $product->name,
            'uom_inventory' => $product->uom_inventory,
            'conversions' => $conversions
        ]);
    }

    // --- INBOUND ---
    public function inboundForm()
    {
        $bins = Bin::with('pallets')->get();
        $pallets = Pallet::where('is_active', true)->get();
        $palletTypes = PalletType::all();
        $products = Product::with('conversions')->get();
        $warehouses = Warehouse::all();

        return view('transactions.inbound', compact('bins', 'pallets', 'palletTypes', 'products', 'warehouses'));
    }

    public function inbound(Request $request)
    {
        $data = $request->validate([
            'bin_id' => 'required|exists:bins,id',
            'pallet_id' => 'required|string|max:50',
            'pallet_type_id' => 'required|exists:pallet_types,id',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'expired_date' => 'required|date',
            'machine_code' => 'required|string|max:50',
            'shift' => 'required|integer|in:1,2,3',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'box_id' => 'nullable|string|max:50',
        ]);

        try {
            $data['qty_input'] = $data['qty'];
            $data['uom_input'] = $data['unit'];
            $box = $this->transactionService->processInbound($data, auth()->id());
            return redirect()->route('transaction.inbound.form')->with('success', "Putaway Barang Masuk Sukses! Box {$box->id} ({$data['qty']} {$data['unit']}) dimasukkan ke Palet {$data['pallet_id']} di BIN {$data['bin_id']}.");
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function inboundPalletToBin(Request $request)
    {
        $data = $request->validate([
            'bin_id' => 'required|exists:bins,id',
            'pallet_id' => 'required|string|max:50',
            'pallet_type_id' => 'required|exists:pallet_types,id',
        ]);

        try {
            $pallet = $this->transactionService->assignPalletToBin($data['bin_id'], $data['pallet_id'], $data['pallet_type_id'], auth()->id());
            return redirect()->route('transaction.inbound.form')->with('success', "Penempatan Palet Sukses! Palet {$pallet->id} kini resmi terdaftar pada BIN {$pallet->bin_id}.");
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function inboundBoxToPallet(Request $request)
    {
        $data = $request->validate([
            'pallet_id' => 'required|exists:pallets,id',
            'box_id' => 'nullable|string|max:50',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'expired_date' => 'required|date',
            'machine_code' => 'required|string|max:50',
            'shift' => 'required|integer|in:1,2,3',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        try {
            $data['qty_input'] = $data['qty'];
            $data['uom_input'] = $data['unit'];
            $box = $this->transactionService->attachBoxToPallet($data, auth()->id());
            return redirect()->route('transaction.inbound.form')->with('success', "Pendataan Box Sukses! Box {$box->id} ({$data['qty']} {$data['unit']}) kini resmi terdaftar pada Palet {$data['pallet_id']}.");
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // --- OUTBOUND ---
    public function outboundForm()
    {
        $pallets = Pallet::with(['bin', 'boxes' => function($q) {
            $q->where('status', 'active')->with('product.conversions');
        }])->where('is_active', true)->whereNotNull('bin_id')->get();

        $products = Product::with('conversions')->get();

        return view('transactions.outbound', compact('pallets', 'products'));
    }

    public function outbound(Request $request)
    {
        $data = $request->validate([
            'pallet_id' => 'required|exists:pallets,id',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.01',
            'shift' => 'required|integer|in:1,2,3',
            'unit' => 'nullable|string|max:50',
            'box_id' => 'nullable|string|max:50',
        ]);

        try {
            $data['qty_input'] = $data['qty'];
            $data['uom_input'] = $data['unit'] ?? null;
            $this->transactionService->processOutbound($data, auth()->id());
            $unitText = $data['unit'] ? $data['unit'] : 'unit';
            return redirect()->route('transaction.outbound.form')->with('success', "Picking Barang Keluar Sukses! {$data['qty']} {$unitText} Produk {$data['product_id']} berhasil dikeluarkan dari Palet {$data['pallet_id']}.");
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // --- TRANSFER ---
    public function transferForm()
    {
        $pallets = Pallet::with(['bin', 'boxes.product'])->where('is_active', true)->get();
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
            $pallet = $this->transactionService->processTransfer($data['pallet_id'], $data['target_bin_id'], auth()->id());
            return redirect()->route('transaction.transfer.form')->with('success', "Pemindahan Palet Sukses! Palet {$pallet->id} kini dipindahkan ke BIN {$pallet->bin_id}.");
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
