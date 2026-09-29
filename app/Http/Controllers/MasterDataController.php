<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Office;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\Rack;
use App\Models\Bin;
use App\Models\PalletType;
use App\Models\Uom;
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
        $uoms = $emptyPaginator;
        $palletTypes = $emptyPaginator;
        $products = $emptyPaginator;

        $flatProducts = collect();
        $brands = collect();
        $itemGroups = collect();
        $groupLevel2s = collect();
        $groupLevel3s = collect();

        // Dropdown Lookup Data (Lazy loaded per active tab)
        $allOffices = collect();
        $allBranches = collect();
        $allWarehouses = collect();
        $allRacks = collect();
        $allPalletTypes = collect();
        $allUoms = Uom::select('id', 'name', 'description')->orderBy('name', 'asc')->get();

        // OPTIMIZED QUERY DISPATCH: Execute database queries ONLY for the active tab!
        switch ($tab) {
            case 'branches':
                $allOffices = Office::select('id', 'name')->orderBy('name', 'asc')->get();

                $branchQuery = Branch::with('office')->withCount('warehouses')->with('warehouses');
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

                $whQuery = Warehouse::with('branch')->withCount('racks')->with('racks');
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

                $rackQuery = Rack::with('warehouse')->withCount('bins')->with('bins');
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

                $binQuery = Bin::with(['rack.warehouse', 'pallets' => function($q) { $q->where('is_active', true); }])->withCount(['pallets as active_pallets_count' => function($q) {
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
                $bins = $binQuery->orderBy($idCol = 'id', $sortDir)
                                 ->paginate(15, ['*'], 'bins_page')
                                 ->withQueryString();
                break;

            case 'uoms':
                $uomQuery = Uom::withCount('products')->with(['products' => function($q) {
                    $q->select('id', 'name', 'brand', 'item_group', 'uom_inventory', 'uom_group', 'karton_to_pcs', 'is_active')->orderBy('id', 'asc');
                }]);
                if ($request->filled('search_uom')) {
                    $s = $request->search_uom;
                    $uomQuery->where(function($q) use ($s) {
                        $q->where('id', 'like', "%{$s}%")
                          ->orWhere('name', 'like', "%{$s}%")
                          ->orWhere('description', 'like', "%{$s}%");
                    });
                }
                $uoms = $uomQuery->orderBy($sortBy === 'name' ? 'name' : 'id', $sortDir)
                                 ->paginate(15, ['*'], 'uoms_page')
                                 ->withQueryString();
                break;

            case 'products':
                $brands = Product::whereNotNull('brand')->where('brand', '!=', '')->distinct()->pluck('brand')->sort()->values();
                $itemGroups = Product::whereNotNull('item_group')->where('item_group', '!=', '')->distinct()->pluck('item_group')->sort()->values();
                $groupLevel2s = Product::whereNotNull('group_level_2')->where('group_level_2', '!=', '')->distinct()->pluck('group_level_2')->sort()->values();
                $groupLevel3s = Product::whereNotNull('group_level_3')->where('group_level_3', '!=', '')->distinct()->pluck('group_level_3')->sort()->values();

                $productQuery = Product::with('conversions');
                if ($request->filled('search_product')) {
                    $search = $request->search_product;
                    $productQuery->where(function($q) use ($search) {
                        $q->where('id', 'like', "%{$search}%")
                          ->orWhere('name', 'like', "%{$search}%")
                          ->orWhere('brand', 'like', "%{$search}%")
                          ->orWhere('group_level_2', 'like', "%{$search}%")
                          ->orWhere('group_level_3', 'like', "%{$search}%")
                          ->orWhere('uom_group', 'like', "%{$search}%");
                    });
                }
                if ($request->filled('brand_filter')) {
                    $productQuery->where('brand', $request->brand_filter);
                }
                if ($request->filled('group_filter')) {
                    $productQuery->where('item_group', $request->group_filter);
                }
                if ($request->filled('group_level_2_filter')) {
                    $productQuery->where('group_level_2', $request->group_level_2_filter);
                }
                if ($request->filled('group_level_3_filter')) {
                    $productQuery->where('group_level_3', $request->group_level_3_filter);
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
                                'group_level_2' => $p->group_level_2,
                                'group_level_3' => $p->group_level_3,
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
                            'group_level_2' => $p->group_level_2,
                            'group_level_3' => $p->group_level_3,
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
                $officeQuery = Office::withCount('branches')->with('branches');
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
                'offices', 'branches', 'warehouses', 'racks', 'bins', 'uoms', 'palletTypes', 'products', 'flatProducts', 'brands', 'itemGroups', 'groupLevel2s', 'groupLevel3s',
                'allOffices', 'allBranches', 'allWarehouses', 'allRacks', 'allPalletTypes', 'allUoms',
                'tab', 'sortBy', 'sortDir'
            ))->render();

            return response()->json([
                'status' => 'success',
                'tab' => $tab,
                'html' => $html
            ]);
        }

        return view('master.index', compact(
            'offices', 'branches', 'warehouses', 'racks', 'bins', 'uoms', 'palletTypes', 'products', 'flatProducts', 'brands', 'itemGroups', 'groupLevel2s', 'groupLevel3s',
            'allOffices', 'allBranches', 'allWarehouses', 'allRacks', 'allPalletTypes', 'allUoms',
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

    public function updateOffice(Request $request, $id)
    {
        $office = Office::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
        ]);

        $office->update($data);
        AuditLogger::log(auth()->id(), 'master', 'UPDATE', "Mengubah Kantor: {$office->name} ({$office->id})");
        $msg = "Kantor {$office->name} ({$office->id}) berhasil diperbarui.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'offices'
            ]);
        }
        return back()->with('success', $msg);
    }

    public function destroyOffice(Request $request, $id)
    {
        $office = Office::findOrFail($id);
        $isSuperAdmin = auth()->user() && auth()->user()->role_id === 'super_admin';
        $branchCount = $office->branches()->count();

        if ($branchCount > 0 && !$isSuperAdmin) {
            $errMsg = "Gagal menghapus: Kantor {$office->name} ({$office->id}) masih memiliki {$branchCount} Plant terdaftar!";
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['status' => 'error', 'message' => $errMsg], 422);
            }
            return back()->with('error', $errMsg);
        }

        $office->delete();
        $auditMsg = "Menghapus Kantor: {$office->name} ({$office->id})" . ($branchCount > 0 ? " (Otorisasi Super Admin, {$branchCount} Plant terkait)" : "");
        AuditLogger::log(auth()->id(), 'master', 'DELETE', $auditMsg);
        $msg = "Kantor {$office->name} ({$office->id}) berhasil dihapus.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'offices'
            ]);
        }
        return back()->with('success', $msg);
    }

    public function updateBranch(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);
        $data = $request->validate([
            'office_id' => 'required|exists:offices,id',
            'name' => 'required|string|max:100',
            'city' => 'nullable|string|max:100',
        ]);

        $branch->update($data);
        AuditLogger::log(auth()->id(), 'master', 'UPDATE', "Mengubah Plant: {$branch->name} ({$branch->id})");
        $msg = "Plant {$branch->name} ({$branch->id}) berhasil diperbarui.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'branches'
            ]);
        }
        return back()->with('success', $msg);
    }

    public function destroyBranch(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);
        $isSuperAdmin = auth()->user() && auth()->user()->role_id === 'super_admin';
        $whCount = $branch->warehouses()->count();

        if ($whCount > 0 && !$isSuperAdmin) {
            $errMsg = "Gagal menghapus: Plant {$branch->name} ({$branch->id}) masih memiliki {$whCount} Gudang terdaftar!";
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['status' => 'error', 'message' => $errMsg], 422);
            }
            return back()->with('error', $errMsg);
        }

        $branch->delete();
        $auditMsg = "Menghapus Plant: {$branch->name} ({$branch->id})" . ($whCount > 0 ? " (Otorisasi Super Admin, {$whCount} Gudang terkait)" : "");
        AuditLogger::log(auth()->id(), 'master', 'DELETE', $auditMsg);
        $msg = "Plant {$branch->name} ({$branch->id}) berhasil dihapus.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'branches'
            ]);
        }
        return back()->with('success', $msg);
    }

    public function updateWarehouse(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
        ]);

        $warehouse->update($data);
        AuditLogger::log(auth()->id(), 'master', 'UPDATE', "Mengubah Gudang: {$warehouse->name} ({$warehouse->id})");
        $msg = "Gudang {$warehouse->name} ({$warehouse->id}) berhasil diperbarui.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'warehouses'
            ]);
        }
        return back()->with('success', $msg);
    }

    public function destroyWarehouse(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $isSuperAdmin = auth()->user() && auth()->user()->role_id === 'super_admin';
        $rackCount = $warehouse->racks()->count();

        if ($rackCount > 0 && !$isSuperAdmin) {
            $errMsg = "Gagal menghapus: Gudang {$warehouse->name} ({$warehouse->id}) masih memiliki {$rackCount} Rak terdaftar!";
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['status' => 'error', 'message' => $errMsg], 422);
            }
            return back()->with('error', $errMsg);
        }

        $warehouse->delete();
        $auditMsg = "Menghapus Gudang: {$warehouse->name} ({$warehouse->id})" . ($rackCount > 0 ? " (Otorisasi Super Admin, {$rackCount} Rak terkait)" : "");
        AuditLogger::log(auth()->id(), 'master', 'DELETE', $auditMsg);
        $msg = "Gudang {$warehouse->name} ({$warehouse->id}) berhasil dihapus.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'warehouses'
            ]);
        }
        return back()->with('success', $msg);
    }

    public function updateRack(Request $request, $id)
    {
        $rack = Rack::findOrFail($id);
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'rack_name' => 'required|string|max:50',
        ]);

        $rack->update($data);
        AuditLogger::log(auth()->id(), 'master', 'UPDATE', "Mengubah Rak: {$rack->rack_name} ({$rack->id})");
        $msg = "Rak {$rack->rack_name} ({$rack->id}) berhasil diperbarui.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'racks'
            ]);
        }
        return back()->with('success', $msg);
    }

    public function destroyRack(Request $request, $id)
    {
        $rack = Rack::findOrFail($id);
        $isSuperAdmin = auth()->user() && auth()->user()->role_id === 'super_admin';
        $binCount = $rack->bins()->count();

        if ($binCount > 0 && !$isSuperAdmin) {
            $errMsg = "Gagal menghapus: Rak {$rack->rack_name} ({$rack->id}) masih memiliki {$binCount} Slot BIN terdaftar!";
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['status' => 'error', 'message' => $errMsg], 422);
            }
            return back()->with('error', $errMsg);
        }

        $rack->delete();
        $auditMsg = "Menghapus Rak: {$rack->rack_name} ({$rack->id})" . ($binCount > 0 ? " (Otorisasi Super Admin, {$binCount} BIN terkait)" : "");
        AuditLogger::log(auth()->id(), 'master', 'DELETE', $auditMsg);
        $msg = "Rak {$rack->rack_name} ({$rack->id}) berhasil dihapus.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'racks'
            ]);
        }
        return back()->with('success', $msg);
    }

    public function updateBin(Request $request, $id)
    {
        $bin = Bin::findOrFail($id);
        $data = $request->validate([
            'rack_id' => 'required|exists:racks,id',
            'column_no' => 'required|integer|min:1',
            'level_no' => 'required|integer|min:1',
            'max_pallet' => 'required|integer|min:1|max:5',
        ]);

        $bin->update($data);
        AuditLogger::log(auth()->id(), 'master', 'UPDATE', "Mengubah BIN: {$bin->id} (Max {$bin->max_pallet} Palet)");
        $msg = "BIN {$bin->id} berhasil diperbarui.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'bins'
            ]);
        }
        return back()->with('success', $msg);
    }

    public function destroyBin(Request $request, $id)
    {
        $bin = Bin::findOrFail($id);
        $activePalletsCount = $bin->pallets()->where('is_active', true)->count();

        if ($activePalletsCount > 0) {
            $errMsg = "Gagal menghapus: BIN {$bin->id} masih ditempati oleh {$activePalletsCount} Palet aktif di gudang!";
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['status' => 'error', 'message' => $errMsg], 422);
            }
            return back()->with('error', $errMsg);
        }

        $bin->delete();
        AuditLogger::log(auth()->id(), 'master', 'DELETE', "Menghapus BIN: {$bin->id}");
        $msg = "BIN {$bin->id} berhasil dihapus.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'bins'
            ]);
        }
        return back()->with('success', $msg);
    }

    public function storeUom(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string|max:50|unique:uoms,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $data['id'] = strtoupper(trim($data['id']));

        Uom::create($data);
        AuditLogger::log(auth()->id(), 'master', 'CREATE', "Menambah UOM: {$data['name']} ({$data['id']})");

        $msg = "Satuan UOM {$data['name']} ({$data['id']}) berhasil ditambahkan.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'uoms'
            ]);
        }

        return back()->with('success', $msg);
    }

    public function updateUom(Request $request, $id)
    {
        $uom = Uom::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $oldName = $uom->name;
        $newName = trim($data['name']);

        DB::transaction(function () use ($uom, $oldName, $newName, $data) {
            $uom->update([
                'name' => $newName,
                'description' => $data['description'] ?? null,
            ]);

            // Sinkronisasi jika nama satuan berubah pada data produk & konversi
            if ($oldName !== $newName) {
                Product::where('uom_inventory', $oldName)->update(['uom_inventory' => $newName]);
                ProductUomConversion::where('uom_convert', $oldName)->update(['uom_convert' => $newName]);
                ProductUomConversion::where('uom_base', $oldName)->update(['uom_base' => $newName]);
            }
        });

        AuditLogger::log(auth()->id(), 'master', 'UPDATE', "Mengubah Satuan UOM: {$uom->name} ({$uom->id})");

        $msg = "Satuan UOM {$uom->name} ({$uom->id}) berhasil diperbarui.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'uoms'
            ]);
        }

        return back()->with('success', $msg);
    }

    public function destroyUom(Request $request, $id)
    {
        $uom = Uom::findOrFail($id);
        $isSuperAdmin = auth()->user() && auth()->user()->role_id === 'super_admin';

        $inUseCount = Product::where('uom_inventory', $uom->name)->orWhere('uom_inventory', $uom->id)->count();
        if ($inUseCount > 0 && !$isSuperAdmin) {
            $errMsg = "Gagal menghapus: Satuan UOM {$uom->name} ({$uom->id}) masih digunakan oleh {$inUseCount} produk SKU!";
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'status' => 'error',
                    'message' => $errMsg
                ], 422);
            }
            return back()->with('error', $errMsg);
        }

        $uom->delete();
        $auditMsg = "Menghapus Satuan UOM: {$uom->name} ({$uom->id})" . ($inUseCount > 0 ? " (Otorisasi Super Admin, {$inUseCount} produk terkait)" : "");
        AuditLogger::log(auth()->id(), 'master', 'DELETE', $auditMsg);

        $msg = "Satuan UOM {$uom->name} ({$uom->id}) berhasil dihapus.";

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'tab' => 'uoms'
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
            'group_level_2' => 'nullable|string|max:100',
            'group_level_3' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'uom_inventory' => 'required|string|max:50',
            'uom_group' => 'nullable|string|max:50',
            'uom_1' => 'nullable|string|max:50',
            'uom_2' => 'nullable|string|max:50',
            'uom_3' => 'nullable|string|max:50',
            'karton_to_pcs' => 'nullable|string|max:50',
            'no_of_units' => 'nullable|integer',
            'qty_per_uom_group' => 'nullable|numeric|min:0',
            'manage_batch_no' => 'nullable|boolean',
            'purchase_item' => 'nullable|boolean',
            'sales_item' => 'nullable|boolean',
            'inventory_item' => 'nullable|boolean',
            'sales_tax_definition' => 'nullable|string|max:50',
            'purchase_tax_definition' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
            'length_cm' => 'nullable|numeric|min:0',
            'width_cm' => 'nullable|numeric|min:0',
            'height_cm' => 'nullable|numeric|min:0',
            'volume_m3' => 'nullable|numeric|min:0',
            'weight_gross_kg' => 'nullable|numeric|min:0',
            'weight_net_g' => 'nullable|numeric|min:0',
            'conversions' => 'nullable|array',
            'conversions.*.uom_convert' => 'nullable|string|max:50',
            'conversions.*.qty_convert' => 'nullable|numeric|min:0.01',
            'conversions.*.uom_base' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($data) {
            $qtyPerGroup = isset($data['qty_per_uom_group']) && is_numeric($data['qty_per_uom_group']) 
                ? (float)$data['qty_per_uom_group'] 
                : (is_numeric($data['karton_to_pcs'] ?? null) ? (float)$data['karton_to_pcs'] : 1);

            $uomGroup = !empty($data['uom_group']) ? trim($data['uom_group']) : null;
            $kartonToPcs = !empty($data['karton_to_pcs']) ? trim($data['karton_to_pcs']) : null;
            $isManualUom = ($uomGroup === 'Manual' || strtolower((string)$kartonToPcs) === 'manual');

            $product = Product::create([
                'id' => strtoupper(trim($data['id'])),
                'name' => trim($data['name']),
                'item_group' => !empty($data['item_group']) ? trim($data['item_group']) : 'FINISHED GOODS (MANUFAKTUR)',
                'group_level_2' => !empty($data['group_level_2']) ? trim($data['group_level_2']) : null,
                'group_level_3' => !empty($data['group_level_3']) ? trim($data['group_level_3']) : null,
                'brand' => !empty($data['brand']) ? trim($data['brand']) : null,
                'uom_inventory' => trim($data['uom_inventory']),
                'uom_group' => $uomGroup,
                'uom_1' => !empty($data['uom_1']) ? trim($data['uom_1']) : null,
                'uom_2' => !empty($data['uom_2']) ? trim($data['uom_2']) : null,
                'uom_3' => !empty($data['uom_3']) ? trim($data['uom_3']) : trim($data['uom_inventory']),
                'karton_to_pcs' => $kartonToPcs,
                'no_of_units' => isset($data['no_of_units']) ? (int)$data['no_of_units'] : 1,
                'qty_per_uom_group' => $qtyPerGroup,
                'is_manual_uom' => $isManualUom,
                'manage_batch_no' => $request->has('manage_batch_no') ? (bool)$request->manage_batch_no : true,
                'purchase_item' => $request->has('purchase_item') ? (bool)$request->purchase_item : false,
                'sales_item' => $request->has('sales_item') ? (bool)$request->sales_item : true,
                'inventory_item' => $request->has('inventory_item') ? (bool)$request->inventory_item : true,
                'sales_tax_definition' => !empty($data['sales_tax_definition']) ? trim($data['sales_tax_definition']) : 'PPN Out 10%',
                'purchase_tax_definition' => !empty($data['purchase_tax_definition']) ? trim($data['purchase_tax_definition']) : 'PPN In 11%',
                'is_active' => (bool)$data['is_active'],
                'length_cm' => isset($data['length_cm']) ? (float)$data['length_cm'] : 0,
                'width_cm' => isset($data['width_cm']) ? (float)$data['width_cm'] : 0,
                'height_cm' => isset($data['height_cm']) ? (float)$data['height_cm'] : 0,
                'volume_m3' => isset($data['volume_m3']) ? (float)$data['volume_m3'] : 0,
                'weight_gross_kg' => isset($data['weight_gross_kg']) ? (float)$data['weight_gross_kg'] : 0,
                'weight_net_g' => isset($data['weight_net_g']) ? (float)$data['weight_net_g'] : 0,
            ]);

            $hasBaseConversion = false;
            if (!empty($data['conversions'])) {
                foreach ($data['conversions'] as $conv) {
                    if (!empty($conv['uom_convert']) && !empty($conv['qty_convert']) && is_numeric($conv['qty_convert'])) {
                        $uomConvert = trim($conv['uom_convert']);
                        $qtyConvert = (float)$conv['qty_convert'];
                        $uomBase = !empty($conv['uom_base']) ? trim($conv['uom_base']) : $product->uom_inventory;

                        if (strtoupper($uomConvert) === strtoupper($product->uom_inventory) && $qtyConvert == 1) {
                            $hasBaseConversion = true;
                        }

                        ProductUomConversion::create([
                            'product_id' => $product->id,
                            'uom_convert' => $uomConvert,
                            'qty_convert' => $qtyConvert,
                            'uom_base' => $uomBase,
                            'multiplier_to_base' => $qtyConvert,
                        ]);
                    }
                }
            }

            // Selalu pastikan konversi dasar 1:1 ada untuk perhitungan stok inventaris
            if (!$hasBaseConversion) {
                ProductUomConversion::firstOrCreate([
                    'product_id' => $product->id,
                    'uom_convert' => $product->uom_inventory,
                ], [
                    'qty_convert' => 1,
                    'uom_base' => $product->uom_inventory,
                    'multiplier_to_base' => 1,
                ]);
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
            'group_level_2' => 'nullable|string|max:100',
            'group_level_3' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'uom_inventory' => 'required|string|max:50',
            'uom_group' => 'nullable|string|max:50',
            'uom_1' => 'nullable|string|max:50',
            'uom_2' => 'nullable|string|max:50',
            'uom_3' => 'nullable|string|max:50',
            'karton_to_pcs' => 'nullable|string|max:50',
            'no_of_units' => 'nullable|integer',
            'qty_per_uom_group' => 'nullable|numeric|min:0',
            'manage_batch_no' => 'nullable|boolean',
            'purchase_item' => 'nullable|boolean',
            'sales_item' => 'nullable|boolean',
            'inventory_item' => 'nullable|boolean',
            'sales_tax_definition' => 'nullable|string|max:50',
            'purchase_tax_definition' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
            'length_cm' => 'nullable|numeric|min:0',
            'width_cm' => 'nullable|numeric|min:0',
            'height_cm' => 'nullable|numeric|min:0',
            'volume_m3' => 'nullable|numeric|min:0',
            'weight_gross_kg' => 'nullable|numeric|min:0',
            'weight_net_g' => 'nullable|numeric|min:0',
        ]);

        if (isset($data['uom_group']) || isset($data['karton_to_pcs'])) {
            $uomGroup = $data['uom_group'] ?? $product->uom_group;
            $kartonToPcs = $data['karton_to_pcs'] ?? $product->karton_to_pcs;
            $data['is_manual_uom'] = ($uomGroup === 'Manual' || strtolower((string)$kartonToPcs) === 'manual');
        }

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
