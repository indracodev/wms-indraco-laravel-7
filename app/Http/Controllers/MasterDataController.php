<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Office;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\Rack;
use App\Models\Bin;
use App\Models\PalletType;
use App\Models\Product;
use App\Models\ProductUomConversion;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'offices');
        $sortBy = $request->get('sort', 'id');
        $sortDir = in_array(strtolower($request->get('dir', 'asc')), ['asc', 'desc']) ? $request->get('dir', 'asc') : 'asc';

        // Empty default paginators for inactive tabs to guarantee minimum memory & execution overhead
        $emptyPaginator = new LengthAwarePaginator([], 0, 10);

        $offices = $emptyPaginator;
        $branches = $emptyPaginator;
        $warehouses = $emptyPaginator;
        $racks = $emptyPaginator;
        $bins = $emptyPaginator;
        $palletTypes = $emptyPaginator;
        $products = $emptyPaginator;

        $flatProducts = collect();
        $brands = collect();
        $itemGroups = collect();

        // Dropdown Lookup Data (Lazy loaded per active tab)
        $allOffices = collect();
        $allBranches = collect();
        $allWarehouses = collect();
        $allRacks = collect();
        $allPalletTypes = collect();

        // OPTIMIZED QUERY DISPATCH: Execute database queries ONLY for the active tab!
        switch ($tab) {
            case 'branches':
                $allOffices = Office::select('id', 'name')->orderBy('name', 'asc')->get();

                $branchQuery = Branch::with('office');
                if ($request->filled('office_filter')) {
                    $branchQuery->where('office_id', $request->office_filter);
                }
                if ($request->filled('search_branch')) {
                    $s = $request->search_branch;
                    $branchQuery->where(function($q) use ($s) {
                        $q->where('id', 'like', "%{$s}%")
                          ->orWhere('name', 'like', "%{$s}%")
                          ->orWhere('city', 'like', "%{$s}%");
                    });
                }
                $branches = $branchQuery->orderBy($sortBy === 'name' ? 'name' : 'id', $sortDir)
                                        ->paginate(10, ['*'], 'branches_page')
                                        ->withQueryString();
                break;

            case 'warehouses':
                $allBranches = Branch::select('id', 'name')->orderBy('name', 'asc')->get();

                $whQuery = Warehouse::with('branch');
                if ($request->filled('branch_filter')) {
                    $whQuery->where('branch_id', $request->branch_filter);
                }
                if ($request->filled('search_wh')) {
                    $s = $request->search_wh;
                    $whQuery->where(function($q) use ($s) {
                        $q->where('id', 'like', "%{$s}%")
                          ->orWhere('name', 'like', "%{$s}%");
                    });
                }
                $warehouses = $whQuery->orderBy($sortBy === 'name' ? 'name' : 'id', $sortDir)
                                       ->paginate(10, ['*'], 'wh_page')
                                       ->withQueryString();
                break;

            case 'racks':
                $allWarehouses = Warehouse::select('id', 'name')->orderBy('name', 'asc')->get();

                // Optimized withCount('bins') instead of loading all bin relations
                $rackQuery = Rack::with('warehouse')->withCount('bins');
                if ($request->filled('wh_filter')) {
                    $rackQuery->where('warehouse_id', $request->wh_filter);
                }
                if ($request->filled('search_rack')) {
                    $s = $request->search_rack;
                    $rackQuery->where(function($q) use ($s) {
                        $q->where('id', 'like', "%{$s}%")
                          ->orWhere('rack_name', 'like', "%{$s}%")
                          ->orWhere('warehouse_id', 'like', "%{$s}%");
                    });
                }
                $racks = $rackQuery->orderBy($sortBy === 'name' ? 'rack_name' : 'id', $sortDir)
                                   ->paginate(10, ['*'], 'racks_page')
                                   ->withQueryString();
                break;

            case 'bins':
                $allRacks = Rack::select('id', 'warehouse_id')->orderBy('id', 'asc')->get();

                // Optimized withCount active pallets instead of loading all pallet collections
                $binQuery = Bin::with('rack.warehouse')->withCount(['pallets as active_pallets_count' => function($q) {
                    $q->where('is_active', true);
                }]);
                if ($request->filled('rack_filter')) {
                    $binQuery->where('rack_id', $request->rack_filter);
                }
                if ($request->filled('search_bin')) {
                    $s = $request->search_bin;
                    $binQuery->where(function($q) use ($s) {
                        $q->where('id', 'like', "%{$s}%")
                          ->orWhere('rack_id', 'like', "%{$s}%");
                    });
                }
                $bins = $binQuery->orderBy('id', $sortDir)
                                 ->paginate(15, ['*'], 'bins_page')
                                 ->withQueryString();
                break;

            case 'products':
                $brands = Product::whereNotNull('brand')->where('brand', '!=', '')->distinct()->pluck('brand')->sort()->values();
                $itemGroups = Product::whereNotNull('item_group')->where('item_group', '!=', '')->distinct()->pluck('item_group')->sort()->values();

                $productQuery = Product::with('conversions');
                if ($request->filled('search_product')) {
                    $search = $request->search_product;
                    $productQuery->where(function($q) use ($search) {
                        $q->where('id', 'like', "%{$search}%")
                          ->orWhere('name', 'like', "%{$search}%")
                          ->orWhere('brand', 'like', "%{$search}%");
                    });
                }
                if ($request->filled('brand_filter')) {
                    $productQuery->where('brand', $request->brand_filter);
                }
                if ($request->filled('group_filter')) {
                    $productQuery->where('item_group', $request->group_filter);
                }
                if ($request->filled('active_filter')) {
                    $productQuery->where('is_active', $request->active_filter === '1');
                }

                $products = $productQuery->orderBy($sortBy === 'name' ? 'name' : 'id', $sortDir)
                                         ->paginate(15, ['*'], 'products_page')
                                         ->withQueryString();

                foreach ($products as $p) {
                    if ($p->conversions->count() > 0) {
                        foreach ($p->conversions as $conv) {
                            $flatProducts->push((object)[
                                'product' => $p,
                                'id' => $p->id,
                                'name' => $p->name,
                                'item_group' => $p->item_group,
                                'uom_inventory' => $p->uom_inventory,
                                'uom_group' => $p->uom_group,
                                'qty_per_uom_group' => $p->qty_per_uom_group,
                                'brand' => $p->brand,
                                'is_active' => $p->is_active,
                                'uom_convert' => $conv->uom_convert,
                                'qty_convert' => $conv->qty_convert,
                                'uom_base' => $conv->uom_base,
                            ]);
                        }
                    } else {
                        $flatProducts->push((object)[
                            'product' => $p,
                            'id' => $p->id,
                            'name' => $p->name,
                            'item_group' => $p->item_group,
                            'uom_inventory' => $p->uom_inventory,
                            'uom_group' => $p->uom_group,
                            'qty_per_uom_group' => $p->qty_per_uom_group,
                            'brand' => $p->brand,
                            'is_active' => $p->is_active,
                            'uom_convert' => $p->uom_inventory,
                            'qty_convert' => 1,
                            'uom_base' => $p->uom_inventory,
                        ]);
                    }
                }
                break;

            case 'offices':
            default:
                $tab = 'offices';
                $officeQuery = Office::withCount('branches');
                if ($request->filled('search_office')) {
                    $s = $request->search_office;
                    $officeQuery->where(function($q) use ($s) {
                        $q->where('id', 'like', "%{$s}%")
                          ->orWhere('name', 'like', "%{$s}%")
                          ->orWhere('address', 'like', "%{$s}%");
                    });
                }
                $offices = $officeQuery->orderBy($sortBy === 'name' ? 'name' : 'id', $sortDir)
                                       ->paginate(10, ['*'], 'offices_page')
                                       ->withQueryString();
                break;
        }

        // AJAX / JSON SPA Response
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $html = view('master.tabs.' . $tab, compact(
                'offices', 'branches', 'warehouses', 'racks', 'bins', 'palletTypes', 'products', 'flatProducts', 'brands', 'itemGroups',
                'allOffices', 'allBranches', 'allWarehouses', 'allRacks', 'allPalletTypes',
                'tab', 'sortBy', 'sortDir'
            ))->render();

            return response()->json([
                'status' => 'success',
                'tab' => $tab,
                'html' => $html
            ]);
        }

        return view('master.index', compact(
            'offices', 'branches', 'warehouses', 'racks', 'bins', 'palletTypes', 'products', 'flatProducts', 'brands', 'itemGroups',
            'allOffices', 'allBranches', 'allWarehouses', 'allRacks', 'allPalletTypes',
            'tab', 'sortBy', 'sortDir'
        ));
    }

    public function storeOffice(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string|unique:offices,id',
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
        ]);

        Office::create($data);
        AuditLogger::log(auth()->id(), 'master', 'CREATE', "Menambah Kantor: {$data['name']} ({$data['id']})");

        $msg = "Kantor {$data['name']} berhasil ditambahkan.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'offices'
            ]);
        }

        return back()->with('success', $msg);
    }

    public function storeBranch(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string|unique:branches,id',
            'office_id' => 'required|exists:offices,id',
            'name' => 'required|string|max:100',
            'city' => 'nullable|string|max:100',
        ]);

        Branch::create($data);
        AuditLogger::log(auth()->id(), 'master', 'CREATE', "Menambah Plant: {$data['name']} ({$data['id']})");

        $msg = "Plant {$data['name']} berhasil ditambahkan.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'branches'
            ]);
        }

        return back()->with('success', $msg);
    }

    public function storeWarehouse(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string|unique:warehouses,id',
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
        ]);

        Warehouse::create($data);
        AuditLogger::log(auth()->id(), 'master', 'CREATE', "Menambah Gudang: {$data['name']} ({$data['id']})");

        $msg = "Gudang {$data['name']} berhasil ditambahkan.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'warehouses'
            ]);
        }

        return back()->with('success', $msg);
    }

    public function storeRack(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string|unique:racks,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'rack_name' => 'required|string|max:50',
        ]);

        Rack::create($data);
        AuditLogger::log(auth()->id(), 'master', 'CREATE', "Menambah Rak: {$data['rack_name']} ({$data['id']})");

        $msg = "Rak {$data['rack_name']} berhasil ditambahkan.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'racks'
            ]);
        }

        return back()->with('success', $msg);
    }

    public function storeBin(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string|unique:bins,id',
            'rack_id' => 'required|exists:racks,id',
            'column_no' => 'required|integer|min:1',
            'level_no' => 'required|integer|min:1',
            'max_pallet' => 'required|integer|min:1|max:5',
        ]);

        Bin::create($data);
        AuditLogger::log(auth()->id(), 'master', 'CREATE', "Menambah BIN: {$data['id']} (Max {$data['max_pallet']} Palet)");

        $msg = "BIN {$data['id']} berhasil ditambahkan.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'bins'
            ]);
        }

        return back()->with('success', $msg);
    }

    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string|max:50|unique:products,id',
            'name' => 'required|string|max:255',
            'item_group' => 'nullable|string|max:100',
            'uom_inventory' => 'required|string|max:50',
            'uom_group' => 'nullable|string|max:50',
            'qty_per_uom_group' => 'required|numeric|min:0',
            'brand' => 'nullable|string|max:100',
            'is_active' => 'required|boolean',
            'conversions' => 'nullable|array',
            'conversions.*.uom_convert' => 'required|string|max:50',
            'conversions.*.qty_convert' => 'required|numeric|min:0.01',
            'conversions.*.uom_base' => 'required|string|max:50',
        ]);

        DB::transaction(function () use ($data, $request) {
            $product = Product::create([
                'id' => $data['id'],
                'name' => $data['name'],
                'item_group' => $data['item_group'] ?? null,
                'uom_inventory' => $data['uom_inventory'],
                'uom_group' => $data['uom_group'] ?? null,
                'qty_per_uom_group' => $data['qty_per_uom_group'],
                'brand' => $data['brand'] ?? null,
                'is_active' => (bool)$data['is_active'],
            ]);

            if (!empty($data['conversions'])) {
                foreach ($data['conversions'] as $conv) {
                    ProductUomConversion::create([
                        'product_id' => $product->id,
                        'uom_convert' => $conv['uom_convert'],
                        'qty_convert' => $conv['qty_convert'],
                        'uom_base' => $conv['uom_base'],
                    ]);
                }
            }
        });

        AuditLogger::log(auth()->id(), 'master', 'CREATE', "Menambah Produk SKU: {$data['name']} ({$data['id']})");

        $msg = "Produk SKU {$data['name']} ({$data['id']}) berhasil ditambahkan.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'products'
            ]);
        }

        return back()->with('success', $msg);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'item_group' => 'nullable|string|max:100',
            'uom_inventory' => 'required|string|max:50',
            'uom_group' => 'nullable|string|max:50',
            'qty_per_uom_group' => 'required|numeric|min:0',
            'brand' => 'nullable|string|max:100',
            'is_active' => 'required|boolean',
        ]);

        $product->update($data);
        AuditLogger::log(auth()->id(), 'master', 'UPDATE', "Mengubah Produk SKU: {$product->name} ({$product->id})");

        $msg = "Produk SKU {$product->name} ({$product->id}) berhasil diperbarui.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'products'
            ]);
        }

        return back()->with('success', $msg);
    }

    public function destroyProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($product->boxes()->where('status', 'active')->count() > 0) {
            $errMsg = "Gagal menghapus: Produk SKU {$product->id} masih terdaftar pada stok box aktif di gudang!";
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'status' => 'error',
                    'message' => $errMsg
                ], 422);
            }
            return back()->with('error', $errMsg);
        }

        DB::transaction(function () use ($product) {
            $product->conversions()->delete();
            $product->delete();
        });

        AuditLogger::log(auth()->id(), 'master', 'DELETE', "Menghapus Produk SKU: {$product->name} ({$product->id})");

        $msg = "Produk SKU {$product->id} berhasil dihapus.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'products'
            ]);
        }

        return back()->with('success', $msg);
    }
}
