<!-- Desktop Table View -->
<div class="hidden md:block glass-panel p-6 rounded-2xl shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-800/90 text-slate-300 uppercase tracking-wider font-semibold">
                <tr>
                    <th class="p-3.5">Box Barcode</th>
                    <th class="p-3.5">Pallet ID & Jenis</th>
                    <th class="p-3.5">Lokasi BIN & Gudang</th>
                    <th class="p-3.5">Produk SKU</th>
                    <th class="p-3.5">Stok Qty</th>
                    <th class="p-3.5">Masa Expired</th>
                    <th class="p-3.5">Kode Mesin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($boxes as $box)
                    <tr class="hover:bg-slate-800/50 transition">
                        <td class="p-3.5 font-mono font-bold text-indigo-400 text-sm">
                            <i class="fa-solid fa-box text-xs mr-1 text-slate-500"></i>
                            {{ $box->id }}
                        </td>
                        <td class="p-3.5">
                            <div class="font-bold text-white font-mono">{{ $box->pallet_id }}</div>
                            <div class="text-[10px] text-slate-400 capitalize">{{ $box->pallet->type->name ?? $box->pallet->pallet_type_id }}</div>
                        </td>
                        <td class="p-3.5">
                            <span class="px-2.5 py-1 rounded font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs">
                                {{ $box->pallet->bin_id ?? 'TRANSIT' }}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-1">
                                {{ $box->pallet->bin->rack->warehouse->name ?? '-' }}
                            </div>
                        </td>
                        <td class="p-3.5">
                            <div class="font-semibold text-white">{{ $box->product->name ?? $box->product_id }}</div>
                            <div class="text-[10px] text-slate-500">SKU: {{ $box->product_id }}</div>
                        </td>
                        <td class="p-3.5 font-extrabold text-emerald-400 text-sm">
                            {{ $box->qty }} <span class="text-xs font-normal text-slate-400">{{ $box->uom_base ?? $box->product->uom_inventory ?? 'Box' }}</span>
                        </td>
                        <td class="p-3.5">
                            <span class="px-2 py-0.5 rounded font-semibold bg-slate-800 text-slate-200 border border-slate-700">
                                {{ \Carbon\Carbon::parse($box->expired_date)->format('d M Y') }}
                            </span>
                        </td>
                        <td class="p-3.5 font-mono text-slate-400">
                            {{ $box->machine_code }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-slate-500 font-medium">
                            Tidak ada data stok barang yang cocok dengan filter pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $boxes->links() }}
    </div>
</div>

<!-- Mobile Flat Card List View -->
<div class="space-y-3 md:hidden">
    @forelse($boxes as $box)
        <div class="bg-slate-800/80 border border-slate-700/70 p-4 rounded-xl space-y-2 text-xs">
            <!-- Header: Box ID + BIN Badge -->
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-2">
                <div class="font-mono font-bold text-indigo-400 text-sm flex items-center">
                    <i class="fa-solid fa-box text-xs mr-1.5 text-slate-400"></i>
                    {{ $box->id }}
                </div>
                <span class="px-2.5 py-0.5 rounded font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs">
                    BIN: {{ $box->pallet->bin_id ?? 'TRANSIT' }}
                </span>
            </div>

            <!-- Product & Stock Qty -->
            <div class="flex items-start justify-between gap-2">
                <div>
                    <div class="font-bold text-white text-sm">{{ $box->product->name ?? $box->product_id }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">SKU: {{ $box->product_id }}</div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="font-extrabold text-emerald-400 text-base leading-none">{{ $box->qty }}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $box->uom_base ?? $box->product->uom_inventory ?? 'Box' }}</div>
                </div>
            </div>

            <!-- Pallet & Expired Date Details -->
            <div class="pt-1 flex items-center justify-between text-[11px] text-slate-400 border-t border-slate-700/40">
                <div>
                    Palet: <span class="font-mono font-semibold text-slate-200">{{ $box->pallet_id }}</span>
                </div>
                <div>
                    Exp: <span class="font-semibold text-amber-300">{{ \Carbon\Carbon::parse($box->expired_date)->format('d M Y') }}</span>
                </div>
                <div>
                    MC: <span class="font-mono text-slate-300">{{ $box->machine_code }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="p-6 text-center text-slate-500 text-xs bg-slate-800/40 rounded-xl">
            Tidak ada data stok barang yang cocok dengan filter pencarian.
        </div>
    @endforelse

    <div class="mt-4">
        {{ $boxes->links() }}
    </div>
</div>
