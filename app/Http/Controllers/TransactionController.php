<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        // Eager Loading dengan seleksi kolom spesifik
        $pallet = Pallet::select('id', 'pallet_type_id', 'bin_id', 'status', 'max_capacity', 'is_active')
            ->with([
                'activeBoxes' => function ($qb) {
                    $qb->select('id', 'pallet_id', 'product_id', 'qty', 'uom_base', 'expired_date', 'machine_code', 'notes')
                       ->with([
                           'product:id,name,brand,uom_inventory',
                           'product.conversions:id,product_id,uom_convert,qty_convert,uom_base'
                       ]);
                },
                'bin:id,rack_id',
                'bin.rack:id,warehouse_id'
            ])
            ->find($id);

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

        // Lazy Eager Loading jika relasi belum termuat
        $pallet->loadMissing(['type:id,name']);

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

    // --- API HELPER: RACK / BIN INVENTORY FOR OUTBOUND PICKING (WITH CACHE + EAGER + LAZY) ---
    public function getRackInventory($id)
    {
        // Cache inventory rack/bin selama 60 detik untuk scan & request cepat (<10ms)
        $cacheKey = 'rack_inventory_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $id);
        
        $result = Cache::remember($cacheKey, 60, function () use ($id) {
            // $id can be a BIN ID (e.g. B.10.1) or Rack ID (e.g. RK-B)
            $bins = Bin::where('id', $id)->orWhere('rack_id', $id)->pluck('id');

            // Eager Loading terarah + Lazy Collection untuk hemat alokasi memori
            $pallets = Pallet::select('id', 'bin_id', 'max_capacity', 'status', 'is_active')
                ->with([
                    'bin:id,rack_id',
                    'bin.rack:id,warehouse_id',
                    'activeBoxes' => function ($q) {
                        $q->select('id', 'pallet_id', 'product_id', 'qty', 'uom_base', 'expired_date', 'machine_code', 'notes')
                          ->orderBy('expired_date', 'asc')
                          ->with([
                              'product:id,name,brand,uom_inventory',
                              'product.conversions:id,product_id,uom_convert,qty_convert,uom_base'
                          ]);
                    }
                ])
                ->whereIn('bin_id', $bins)
                ->where('is_active', true)
                ->cursor();

            $items = [];
            foreach ($pallets as $plt) {
                // Lazy Eager Loading jika relasi bertingkat bin.rack belum terisi
                $plt->loadMissing('bin.rack');
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

            return [
                'status' => 'success',
                'rack_or_bin_id' => $id,
                'items_count' => count($items),
                'items' => $items,
            ];
        });

        return response()->json($result);
    }

    // --- INBOUND ---
    public function inboundForm()
    {
        // 1. Cache master data yang jarang berubah untuk load cepat (<50ms)
        // Eager loading spesifik kolom pada BIN dan relasi rack
        $bins = Cache::remember('inbound_bins_cache', 300, function () {
            return Bin::select('id', 'rack_id', 'max_pallet')
                ->with('rack:id,warehouse_id')
                ->withCount(['pallets' => function ($q) {
                    $q->where('is_active', true);
                }])
                ->havingRaw('pallets_count < max_pallet')
                ->limit(100)
                ->get();
        });

        $palletTypes = Cache::remember('inbound_pallet_types_cache', 3600, function () {
            return PalletType::select('id', 'name')->get();
        });

        // Eager Loading produk beserta relasi konversi satuannya
        $products = Cache::remember('inbound_products_cache', 3600, function () {
            return Product::select('id', 'name', 'brand', 'uom_inventory')
                ->with(['conversions:id,product_id,uom_convert,qty_convert,uom_base'])
                ->get();
        });

        $warehouses = Cache::remember('inbound_warehouses_cache', 3600, function () {
            return Warehouse::select('id', 'name')->get();
        });

        // Eager Loading Rak dengan kolom terpilih pada Bins
        $racks = Cache::remember('inbound_racks_cache', 3600, function () {
            return Rack::select('id', 'rack_name', 'warehouse_id')
                ->with(['bins:id,rack_id,max_pallet'])
                ->get();
        });

        // 2. Optimasi Palet: Eager Loading selektif + Lazy Eager Loading (loadMissing)
        $pallets = Cache::remember('inbound_initial_pallets', 60, function () {
            $list = Pallet::select('id', 'pallet_type_id', 'bin_id', 'status', 'max_capacity', 'is_active')
                ->with([
                    'type:id,name',
                    'activeBoxes' => function ($qb) {
                        $qb->select('id', 'pallet_id', 'product_id', 'qty', 'uom_base')
                           ->with('product:id,name,brand');
                    }
                ])
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->has('activeBoxes')
                      ->orWhere('status', 'in_transit');
                })
                ->orderByRaw("CASE WHEN bin_id IS NOT NULL THEN 0 ELSE 1 END")
                ->limit(60)
                ->get();

            // Lazy Eager Loading: loadMissing untuk memastikan type dan bin ter-cache tanpa query ganda
            $list->loadMissing(['type:id,name', 'bin:id,rack_id']);
            return $list;
        });

        // Daftar palet in_transit khusus untuk tab Putaway (Eager Loading bertingkat terpilih)
        $putawayPallets = Cache::remember('inbound_putaway_pallets', 30, function () {
            $list = Pallet::select('id', 'pallet_type_id', 'bin_id', 'status', 'max_capacity')
                ->where('status', 'in_transit')
                ->has('activeBoxes')
                ->with([
                    'activeBoxes' => function ($qb) {
                        $qb->select('id', 'pallet_id', 'product_id', 'qty', 'uom_base')
                           ->with('product:id,name');
                    }
                ])
                ->get();

            // Lazy Eager Loading jika ada atribut belum termuat
            $list->loadMissing('activeBoxes.product:id,name');
            return $list;
        });

        return view('transactions.inbound', compact('bins', 'racks', 'pallets', 'putawayPallets', 'palletTypes', 'products', 'warehouses'));
    }

    /**
     * API Pencarian Palet Cepat & Ringan dengan Cache, Eager Loading & Lazy Collection
     */
    public function searchPallets(Request $request)
    {
        $q = strtoupper(trim($request->get('q', '')));
        $cacheKey = 'pallets_search_' . md5($q);

        $results = Cache::remember($cacheKey, 60, function () use ($q) {
            $query = Pallet::select('id', 'pallet_type_id', 'bin_id', 'status', 'max_capacity')
                ->where('is_active', true);

            if (!empty($q)) {
                $query->where('id', 'like', "%{$q}%");
            }

            // Eager Loading terarah + Lazy Collection streaming untuk meminimalkan beban memori
            return $query->with([
                    'type:id,name',
                    'activeBoxes' => function ($qb) {
                        $qb->select('id', 'pallet_id', 'product_id', 'qty', 'uom_base');
                    }
                ])
                ->orderByRaw("CASE WHEN bin_id IS NOT NULL THEN 0 ELSE 1 END")
                ->limit(40)
                ->cursor() // Lazy Collection streaming via Cursor Generator
                ->map(function ($p) {
                    $firstBox = $p->activeBoxes->first();
                    $boxCount = $p->activeBoxes->count();
                    $totalQty = $p->activeBoxes->sum('qty');
                    $location = $p->bin_id ? 'Rak ' . $p->bin_id : 'In Transit';
                    $typeName = $p->type->name ?? $p->pallet_type_id;

                    $label = "Palet: {$p->id} ({$location})";
                    if ($firstBox) {
                        $label .= " - SKU: {$firstBox->product_id} ({$totalQty} {$firstBox->uom_base})";
                    }

                    return [
                        'id' => $p->id,
                        'pallet_type_id' => $p->pallet_type_id,
                        'type_name' => $typeName,
                        'bin_id' => $p->bin_id,
                        'location' => $location,
                        'status' => $p->status,
                        'max_capacity' => $p->max_capacity ?? 72,
                        'has_content' => $boxCount > 0,
                        'product_id' => $firstBox ? $firstBox->product_id : null,
                        'total_qty' => $totalQty,
                        'label' => $label,
                    ];
                })
                ->values()
                ->all();
        });

        return response()->json($results);
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
        // 1. Optimasi Tinggi: Eager Loading Terarah + Lazy Collection (300 Detik Cache)
        // Mencegah query ribuan record & alokasi memori berlebih dengan PHP Generator streaming
        $binsData = Cache::remember('outbound_bins_data', 300, function () {
            return Bin::select('id', 'rack_id')
                ->with([
                    'rack:id,warehouse_id',
                    'rack.warehouse:id,name',
                    'pallets' => function ($q) {
                        $q->select('id', 'bin_id', 'is_active')
                          ->where('is_active', true)
                          ->withCount('activeBoxes');
                    }
                ])
                ->where(function ($query) {
                    $query->whereHas('pallets', function ($p) {
                        $p->where('is_active', true);
                    })
                    ->orWhere('id', 'like', 'A.%')
                    ->orWhere('id', 'like', 'B.%');
                })
                ->limit(100)
                ->cursor() // Lazy Collection streaming via Cursor Generator
                ->map(function ($b) {
                    // Lazy Eager Loading: loadMissing jika relasi rack belum terhidrasi
                    $b->loadMissing('rack.warehouse');
                    $activePlts = $b->pallets;
                    $totalBoxes = $activePlts->sum('active_boxes_count');
                    return [
                        'id' => $b->id,
                        'rack_id' => $b->rack_id ?? ($b->rack->id ?? '-'),
                        'warehouse_name' => ($b->rack && $b->rack->warehouse) ? $b->rack->warehouse->name : 'Gudang Utama',
                        'active_pallets_count' => $activePlts->count(),
                        'total_boxes_count' => $totalBoxes,
                        'has_stock' => $activePlts->count() > 0,
                    ];
                })
                ->values()
                ->all();
        });

        return view('transactions.outbound', compact('binsData'));
    }

    public function searchBins(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $tab = $request->input('tab', 'all');
        $cacheKey = 'bins_search_' . md5($q . '_' . $tab);

        $results = Cache::remember($cacheKey, 60, function () use ($q, $tab) {
            $query = Bin::select('id', 'rack_id')
                ->with([
                    'rack:id,warehouse_id',
                    'rack.warehouse:id,name',
                    'pallets' => function ($p) {
                        $p->select('id', 'bin_id', 'is_active')
                          ->where('is_active', true)
                          ->withCount('activeBoxes');
                    }
                ]);

            if (!empty($q)) {
                $query->where(function ($w) use ($q) {
                    $w->where('id', 'like', "%{$q}%")
                      ->orWhere('rack_id', 'like', "%{$q}%");
                });
            }

            if ($tab === 'has_stock') {
                $query->whereHas('pallets', function ($p) {
                    $p->where('is_active', true);
                });
            } elseif ($tab === 'empty') {
                $query->whereDoesntHave('pallets', function ($p) {
                    $p->where('is_active', true);
                });
            }

            // Lazy Collection streaming via Cursor Generator untuk efisiensi memori
            return $query->limit(60)
                ->cursor()
                ->map(function ($b) {
                    $b->loadMissing('rack.warehouse');
                    $activePlts = $b->pallets;
                    $totalBoxes = $activePlts->sum('active_boxes_count');
                    return [
                        'id' => $b->id,
                        'rack_id' => $b->rack_id ?? ($b->rack->id ?? '-'),
                        'warehouse_name' => ($b->rack && $b->rack->warehouse) ? $b->rack->warehouse->name : 'Gudang Utama',
                        'active_pallets_count' => $activePlts->count(),
                        'total_boxes_count' => $totalBoxes,
                        'has_stock' => $activePlts->count() > 0,
                    ];
                })
                ->values()
                ->all();
        });

        return response()->json([
            'status' => 'success',
            'bins' => $results,
        ]);
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

            // Invalidate cache setelah mutasi outbound sukses
            Cache::forget('outbound_bins_data');
            Cache::forget('inbound_bins_cache');
            Cache::forget('inbound_initial_pallets');
            Cache::forget('inbound_putaway_pallets');
            if (!empty($data['rack_id'])) {
                $safeKey = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $data['rack_id']);
                Cache::forget('rack_inventory_' . $safeKey);
            }

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
        // 1. Optimasi Query Pallet dengan Eager Loading & Limit (Mencegah fetch 11.000 record)
        $pallets = Cache::remember('transfer_pallets_cache', 60, function () {
            return Pallet::select('id', 'pallet_type_id', 'bin_id', 'status', 'max_capacity', 'is_active')
                ->with([
                    'type:id,name',
                    'bin:id,rack_id',
                    'activeBoxes' => function ($qb) {
                        $qb->select('id', 'pallet_id', 'product_id', 'qty', 'uom_base', 'expired_date')
                           ->with('product:id,name,brand');
                    }
                ])
                ->where('is_active', true)
                ->orderByRaw("CASE WHEN bin_id IS NOT NULL THEN 0 ELSE 1 END")
                ->limit(100)
                ->get();
        });

        // 2. Data Awal Slot BIN untuk Modal Pencarian Cepat (Cache 300 Detik)
        $binsData = Cache::remember('outbound_bins_data', 300, function () {
            return Bin::select('id', 'rack_id')
                ->with([
                    'rack:id,warehouse_id',
                    'rack.warehouse:id,name',
                    'pallets' => function ($q) {
                        $q->select('id', 'bin_id', 'is_active')
                          ->where('is_active', true)
                          ->withCount('activeBoxes');
                    }
                ])
                ->where(function ($query) {
                    $query->whereHas('pallets', function ($p) {
                        $p->where('is_active', true);
                    })
                    ->orWhere('id', 'like', 'A.%')
                    ->orWhere('id', 'like', 'B.%');
                })
                ->limit(100)
                ->cursor()
                ->map(function ($b) {
                    $b->loadMissing('rack.warehouse');
                    $activePlts = $b->pallets;
                    $totalBoxes = $activePlts->sum('active_boxes_count');
                    return [
                        'id' => $b->id,
                        'rack_id' => $b->rack_id ?? ($b->rack->id ?? '-'),
                        'warehouse_name' => ($b->rack && $b->rack->warehouse) ? $b->rack->warehouse->name : 'Gudang Utama',
                        'active_pallets_count' => $activePlts->count(),
                        'total_boxes_count' => $totalBoxes,
                        'has_stock' => $activePlts->count() > 0,
                    ];
                })
                ->values()
                ->all();
        });

        // 3. Ringkasan Palet untuk Modal Pencarian Palet Cepat & Mobile-Friendly
        $palletsData = $pallets->map(function ($p) {
            $firstBox = $p->activeBoxes->first();
            $boxCount = $p->activeBoxes->count();
            $totalQty = $p->activeBoxes->sum('qty');
            $location = $p->bin_id ? 'Rak ' . $p->bin_id : 'In Transit';
            $typeName = $p->type->name ?? $p->pallet_type_id;

            $label = "Palet: {$p->id} ({$location})";
            if ($firstBox) {
                $label .= " - SKU: {$firstBox->product_id} ({$totalQty} {$firstBox->uom_base})";
            }

            return [
                'id' => $p->id,
                'pallet_type_id' => $p->pallet_type_id,
                'type_name' => $typeName,
                'bin_id' => $p->bin_id,
                'location' => $location,
                'status' => $p->status,
                'max_capacity' => $p->max_capacity ?? 72,
                'has_content' => $boxCount > 0,
                'box_count' => $boxCount,
                'product_id' => $firstBox ? $firstBox->product_id : null,
                'product_name' => ($firstBox && $firstBox->product) ? $firstBox->product->name : ($firstBox ? $firstBox->product_id : '-'),
                'total_qty' => $totalQty,
                'label' => $label,
            ];
        })->values()->all();

        return view('transactions.transfer', compact('pallets', 'palletsData', 'binsData'));
    }

    public function transfer(Request $request)
    {
        $data = $request->validate([
            'pallet_id' => 'required|exists:pallets,id',
            'target_bin_id' => 'required|exists:bins,id',
        ]);

        try {
            $pallet = $this->transactionService->assignPalletToRack($data['pallet_id'], $data['target_bin_id'], auth()->id());
            
            // Invalidate cache
            Cache::forget('transfer_pallets_cache');
            Cache::forget('outbound_bins_data');
            Cache::forget('inbound_bins_cache');
            Cache::forget('inbound_initial_pallets');
            Cache::forget('inbound_putaway_pallets');
            if (!empty($data['target_bin_id'])) {
                $safeKey = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $data['target_bin_id']);
                Cache::forget('rack_inventory_' . $safeKey);
            }

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
            
            // Invalidate cache
            Cache::forget('transfer_pallets_cache');
            Cache::forget('outbound_bins_data');
            Cache::forget('inbound_initial_pallets');
            Cache::forget('inbound_putaway_pallets');

            return redirect()->route('transaction.transfer.form')->with('success', "Transfer Antar Palet Sukses! {$data['qty']} {$data['unit']} berhasil dipindahkan dari Palet {$data['source_pallet_id']} ke Palet {$data['target_pallet_id']}.");
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
