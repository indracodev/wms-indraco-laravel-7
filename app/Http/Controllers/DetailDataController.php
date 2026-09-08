<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bin;
use App\Models\Pallet;
use App\Models\Box;
use App\Models\Product;

class DetailDataController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterProduct = $request->input('product_id');
        $filterBin = $request->input('bin_id');

        $query = Box::with(['pallet.bin.rack.warehouse', 'product', 'pallet.type']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('pallet_id', 'like', "%{$search}%")
                  ->orWhere('product_id', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pallet', function ($pq) use ($search) {
                      $pq->where('bin_id', 'like', "%{$search}%");
                  });
            });
        }

        if ($filterProduct) {
            $query->where('product_id', $filterProduct);
        }

        if ($filterBin) {
            $query->whereHas('pallet', function ($q) use ($filterBin) {
                $q->where('bin_id', $filterBin);
            });
        }

        $boxes = $query->latest()->paginate(15)->withQueryString();

        // Optimized selective lookup queries to reduce memory footprint
        $products = Product::select('id', 'name')->orderBy('name', 'asc')->get();
        $bins = Bin::select('id', 'rack_id')->orderBy('id', 'asc')->get();

        // AJAX / JSON Response for SPA Zero Page Reload
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $html = view('detail.partials.table', compact('boxes', 'products', 'bins', 'search', 'filterProduct', 'filterBin'))->render();
            return response()->json([
                'status' => 'success',
                'html' => $html
            ]);
        }

        return view('detail.index', compact('boxes', 'products', 'bins', 'search', 'filterProduct', 'filterBin'));
    }
}
