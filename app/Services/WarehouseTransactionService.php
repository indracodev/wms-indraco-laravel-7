<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Bin;
use App\Models\Pallet;
use App\Models\Box;
use App\Models\Product;
use App\Models\ProductUomConversion;
use App\Models\TransactionLog;
use Exception;

class WarehouseTransactionService
{
    /**
     * Resolves UOM conversion info for a product
     */
    public function resolveConversion(string $productId, ?string $uomInput): array
    {
        $product = Product::find($productId);
        $baseUnit = $product ? $product->uom_inventory : 'Box';

        if (empty($uomInput)) {
            return [
                'conversion_factor' => 1.0,
                'uom_base' => $baseUnit
            ];
        }

        $conv = ProductUomConversion::where('product_id', $productId)
                    ->where('uom_convert', $uomInput)
                    ->first();

        if ($conv) {
            return [
                'conversion_factor' => (float) $conv->qty_convert,
                'uom_base' => $conv->uom_base ?: $baseUnit
            ];
        }

        return [
            'conversion_factor' => 1.0,
            'uom_base' => $uomInput ?: $baseUnit
        ];
    }

    /**
     * KETENTUAN 1: Validasi satu palet pertama kali terima hanya bisa input satu macam produk
     */
    public function validateSingleProductRule(string $palletId, string $newProductId): void
    {
        $existingPallet = Pallet::find($palletId);
        if (!$existingPallet) {
            return;
        }

        $activeBoxes = Box::where('pallet_id', $palletId)
                          ->where('status', 'active')
                          ->with('product')
                          ->get();

        $activeProductIds = $activeBoxes->pluck('product_id')->unique()->values();

        if ($activeProductIds->isNotEmpty() && !$activeProductIds->contains($newProductId)) {
            $existingProduct = $activeBoxes->first()->product;
            $existingName = $existingProduct ? "{$existingProduct->name} ({$existingProduct->id})" : $activeProductIds->first();
            throw new Exception("Validasi Gagal: Palet {$palletId} saat penerimaan pertama hanya dapat diisi 1 macam produk ({$existingName}). Gunakan fitur Transfer Antar Palet untuk mencampur produk.");
        }
    }

    /**
     * INBOUND V2: Urutan scan palet -> scan produk (varian) -> isi Qty, Expired date, keterangan -> scan rak
     * Mendukung:
     * - Ketentuan 1: 1 Palet pertama kali terima hanya 1 macam produk
     * - Ketentuan 5: Kapasitas palet (18, 72, 84 unit)
     * - Ketentuan 6: Multi-ED per palet
     * - Ketentuan 7: Status default in_transit jika belum ditaruh di rak
     */
    public function processInboundV2(array $data, string $userId): Box
    {
        return DB::transaction(function () use ($data, $userId) {
            // 1. Validasi Ketentuan 1: 1 palet hanya 1 produk saat penerimaan pertama
            $this->validateSingleProductRule($data['pallet_id'], $data['product_id']);

            // 2. Evaluasi status rak & penempatan (Ketentuan 7: default in_transit jika belum ke rak)
            $hasRack = !empty($data['bin_id']) && $data['bin_id'] !== 'TRANSIT';
            $binId = null;
            $warehouseId = $data['warehouse_id'] ?? 'GSK.678.I';
            $palletStatus = 'in_transit';

            if ($hasRack) {
                $bin = Bin::where('id', $data['bin_id'])->lockForUpdate()->firstOrFail();
                $currentPalletCount = Pallet::where('bin_id', $bin->id)
                                            ->where('is_active', true)
                                            ->where('id', '!=', $data['pallet_id'])
                                            ->count();

                if ($currentPalletCount >= $bin->max_pallet) {
                    throw new Exception("Gagal: BIN {$bin->id} sudah penuh ({$currentPalletCount} Palet terdaftar). Maksimal {$bin->max_pallet} Palet per BIN.");
                }

                $binId = $bin->id;
                $palletStatus = 'on_rack';
                $warehouseId = $bin->rack ? $bin->rack->warehouse_id : $warehouseId;
            }

            // 3. Upsert Palet dengan Kapasitas (18, 72, 84) dan Status
            $maxCapacity = isset($data['max_capacity']) ? (int) $data['max_capacity'] : 72;
            if (!in_array($maxCapacity, [18, 72, 84])) {
                $maxCapacity = 72;
            }

            $pallet = Pallet::updateOrCreate(
                ['id' => $data['pallet_id']],
                [
                    'pallet_type_id' => $data['pallet_type_id'] ?? 'loscam',
                    'bin_id' => $binId,
                    'status' => $palletStatus,
                    'max_capacity' => $maxCapacity,
                    'is_active' => true,
                ]
            );

            // 4. Hitung Konversi UOM ke Satuan Terkecil (Base Unit)
            $qtyInput = (float) $data['qty'];
            $uomInput = $data['unit'] ?? 'Box';
            $convInfo = $this->resolveConversion($data['product_id'], $uomInput);
            $qtyBase = $qtyInput * $convInfo['conversion_factor'];
            $uomBase = $convInfo['uom_base'];

            // 5. Cek Multi-ED (Ketentuan 6) dan simpan Box
            $boxId = !empty($data['box_id']) ? $data['box_id'] : ('BOX-' . strtoupper(substr(md5(uniqid()), 0, 6)));
            
            // Cari apakah sudah ada box dengan produk dan ED yang sama di palet ini
            $existingBox = Box::where('pallet_id', $pallet->id)
                              ->where('product_id', $data['product_id'])
                              ->where('expired_date', $data['expired_date'])
                              ->where('status', 'active')
                              ->first();

            if ($existingBox && empty($data['box_id'])) {
                $existingBox->qty += $qtyBase;
                if (!empty($data['notes'])) {
                    $existingBox->notes = ($existingBox->notes ? $existingBox->notes . ' | ' : '') . $data['notes'];
                }
                $existingBox->save();
                $box = $existingBox;
            } else {
                $box = Box::create([
                    'id' => $boxId,
                    'pallet_id' => $pallet->id,
                    'product_id' => $data['product_id'],
                    'qty' => $qtyBase,
                    'initial_qty' => $qtyBase,
                    'uom_base' => $uomBase,
                    'expired_date' => $data['expired_date'],
                    'machine_code' => $data['machine_code'] ?? 'MC-01',
                    'status' => 'active',
                    'notes' => $data['notes'] ?? null,
                ]);
            }

            // 6. Catat Log Transaksi
            $logNotes = !empty($data['notes']) ? $data['notes'] : "Inbound {$qtyInput} {$uomInput} ({$qtyBase} {$uomBase}) ke Palet {$pallet->id} (Status: {$palletStatus})";
            TransactionLog::create([
                'pic_user_id' => $userId,
                'shift' => $data['shift'] ?? 1,
                'transaction_date' => now(),
                'product_id' => $data['product_id'],
                'qty' => $qtyBase,
                'unit' => $uomInput,
                'uom_input' => $uomInput,
                'qty_input' => $qtyInput,
                'conversion_factor' => $convInfo['conversion_factor'],
                'uom_base' => $uomBase,
                'qty_base' => $qtyBase,
                'expired_date' => $data['expired_date'],
                'machine_code' => $data['machine_code'] ?? 'MC-01',
                'bin_id' => $binId,
                'status' => 'In',
                'pallet_id' => $pallet->id,
                'pallet_type_id' => $pallet->pallet_type_id,
                'box_id' => $box->id,
                'warehouse_id' => $warehouseId,
                'notes' => $logNotes,
            ]);

            AuditLogger::log($userId, 'inbound', 'CREATE', "Barang Masuk V2: {$qtyInput} {$uomInput} Produk {$data['product_id']} (ED: {$data['expired_date']}) ke Palet {$pallet->id} [Status: {$palletStatus}, Kapasitas: {$pallet->max_capacity}]");

            return $box;
        });
    }

    /**
     * OUTBOUND V2: Urutan scan rak -> pilih keluar -> status keluar (Keluar Ekspedisi atau Keluar In Transit)
     * - Ketentuan 4: Urutan scan rak -> pilih keluar (status keluar ekspedisi & keluar in transit)
     */
    public function processOutboundV2(array $data, string $userId): bool
    {
        return DB::transaction(function () use ($data, $userId) {
            $outboundType = $data['outbound_type'] ?? 'ekspedisi'; // 'ekspedisi' atau 'in_transit'
            $pallet = Pallet::where('id', $data['pallet_id'])->lockForUpdate()->firstOrFail();
            $binId = $pallet->bin_id ?? $data['bin_id'] ?? null;
            $warehouseId = $pallet->bin && $pallet->bin->rack ? $pallet->bin->rack->warehouse_id : 'GSK.678.I';

            $box = Box::where('id', $data['box_id'])->lockForUpdate()->firstOrFail();
            $qtyInput = (float) $data['qty'];
            $convInfo = $this->resolveConversion($box->product_id, $data['unit'] ?? $box->uom_base);
            $qtyBaseNeeded = $qtyInput * $convInfo['conversion_factor'];

            if ((float) $box->qty < $qtyBaseNeeded) {
                throw new Exception("Gagal: Qty pengambilan ({$qtyInput} {$data['unit']}) melebihi stok yang tersedia pada Box {$box->id} ({$box->qty} {$box->uom_base}).");
            }

            if ($outboundType === 'ekspedisi') {
                // Potong stok permanen (Barang keluar untuk ekspedisi pengiriman)
                $box->qty = (float) $box->qty - $qtyBaseNeeded;
                if ($box->qty <= 0) {
                    $box->qty = 0;
                    $box->status = 'empty';
                }
                $box->save();

                // Cek sisa box aktif pada palet
                $activeBoxesCount = Box::where('pallet_id', $pallet->id)->where('status', 'active')->count();
                if ($activeBoxesCount === 0) {
                    $pallet->status = 'empty';
                    $pallet->is_active = false;
                    $pallet->bin_id = null;
                    $pallet->save();
                }

                $logStatus = 'Out_Ekspedisi';
                $notes = "Outbound Ekspedisi: {$qtyInput} {$data['unit']} ({$qtyBaseNeeded} {$box->uom_base}) dari Rak {$binId} ke Pengiriman Ekspedisi";
                AuditLogger::log($userId, 'outbound', 'DELETE', "Keluar Ekspedisi: {$qtyInput} {$data['unit']} Produk {$box->product_id} dari Palet {$pallet->id} di Rak {$binId}");
            } else {
                // Keluar In Transit: Palet dilepaskan dari rak dan berstatus in_transit
                $pallet->bin_id = null;
                $pallet->status = 'in_transit';
                $pallet->save();

                $logStatus = 'Out_Transit';
                $notes = "Outbound In Transit: Palet {$pallet->id} diturunkan dari Rak {$binId} ke Staging / Transit";
                AuditLogger::log($userId, 'outbound', 'UPDATE', "Keluar In Transit: Palet {$pallet->id} dilepas dari Rak {$binId} ke status in_transit");
            }

            // Catat Log Transaksi Outbound
            TransactionLog::create([
                'pic_user_id' => $userId,
                'shift' => $data['shift'] ?? 1,
                'transaction_date' => now(),
                'product_id' => $box->product_id,
                'qty' => $qtyBaseNeeded,
                'unit' => $data['unit'] ?? $box->uom_base,
                'uom_input' => $data['unit'] ?? $box->uom_base,
                'qty_input' => $qtyInput,
                'conversion_factor' => $convInfo['conversion_factor'],
                'uom_base' => $box->uom_base,
                'qty_base' => $qtyBaseNeeded,
                'expired_date' => $box->expired_date,
                'machine_code' => $box->machine_code,
                'bin_id' => $binId,
                'status' => $logStatus,
                'pallet_id' => $pallet->id,
                'pallet_type_id' => $pallet->pallet_type_id,
                'box_id' => $box->id,
                'warehouse_id' => $warehouseId,
                'notes' => $notes,
            ]);

            return true;
        });
    }

    /**
     * KETENTUAN 2: Transfer Produk Antar Palet (Mixed-Pallet Transfer)
     * Mengizinkan penerimaan produk berbeda pada palet tujuan
     */
    public function transferProductBetweenPallets(array $data, string $userId): array
    {
        return DB::transaction(function () use ($data, $userId) {
            $sourcePallet = Pallet::where('id', $data['source_pallet_id'])->lockForUpdate()->firstOrFail();
            $targetPallet = Pallet::where('id', $data['target_pallet_id'])->lockForUpdate()->firstOrFail();

            $sourceBox = Box::where('id', $data['source_box_id'])
                            ->where('pallet_id', $sourcePallet->id)
                            ->lockForUpdate()
                            ->firstOrFail();

            $qtyInput = (float) $data['qty'];
            $convInfo = $this->resolveConversion($sourceBox->product_id, $data['unit'] ?? $sourceBox->uom_base);
            $qtyBaseNeeded = $qtyInput * $convInfo['conversion_factor'];

            if ((float) $sourceBox->qty < $qtyBaseNeeded) {
                throw new Exception("Gagal Transfer: Qty yang dipindahkan ({$qtyInput}) melebihi stok yang ada pada Palet Asal ({$sourceBox->qty} {$sourceBox->uom_base}).");
            }

            // Kurangi dari Box Asal
            $sourceBox->qty -= $qtyBaseNeeded;
            if ($sourceBox->qty <= 0) {
                $sourceBox->qty = 0;
                $sourceBox->status = 'empty';
            }
            $sourceBox->save();

            // Tambahkan ke Palet Tujuan (TIDAK ADA RESTRIKSI 1 PRODUK - Ketentuan 2)
            $targetBox = Box::where('pallet_id', $targetPallet->id)
                            ->where('product_id', $sourceBox->product_id)
                            ->where('expired_date', $sourceBox->expired_date)
                            ->where('status', 'active')
                            ->first();

            if ($targetBox) {
                $targetBox->qty += $qtyBaseNeeded;
                $targetBox->save();
            } else {
                $targetBoxId = 'BOX-' . strtoupper(substr(md5(uniqid()), 0, 6));
                $targetBox = Box::create([
                    'id' => $targetBoxId,
                    'pallet_id' => $targetPallet->id,
                    'product_id' => $sourceBox->product_id,
                    'qty' => $qtyBaseNeeded,
                    'initial_qty' => $qtyBaseNeeded,
                    'uom_base' => $sourceBox->uom_base,
                    'expired_date' => $sourceBox->expired_date,
                    'machine_code' => $sourceBox->machine_code,
                    'status' => 'active',
                    'notes' => "Transfer dari Palet {$sourcePallet->id}",
                ]);
            }

            // Cek status palet asal jika habis
            $sourceActiveCount = Box::where('pallet_id', $sourcePallet->id)->where('status', 'active')->count();
            if ($sourceActiveCount === 0) {
                $sourcePallet->status = 'empty';
                $sourcePallet->is_active = false;
                $sourcePallet->bin_id = null;
                $sourcePallet->save();
            }

            $targetPallet->is_active = true;
            $targetPallet->save();

            // Catat Log Transfer
            TransactionLog::create([
                'pic_user_id' => $userId,
                'shift' => $data['shift'] ?? 1,
                'transaction_date' => now(),
                'product_id' => $sourceBox->product_id,
                'qty' => $qtyBaseNeeded,
                'unit' => $data['unit'] ?? $sourceBox->uom_base,
                'uom_input' => $data['unit'] ?? $sourceBox->uom_base,
                'qty_input' => $qtyInput,
                'conversion_factor' => $convInfo['conversion_factor'],
                'uom_base' => $sourceBox->uom_base,
                'qty_base' => $qtyBaseNeeded,
                'expired_date' => $sourceBox->expired_date,
                'machine_code' => $sourceBox->machine_code,
                'bin_id' => $targetPallet->bin_id ?? 'TRANSIT',
                'status' => 'Transfer_Pallet',
                'pallet_id' => $targetPallet->id,
                'box_id' => $targetBox->id,
                'warehouse_id' => 'GSK.678.I',
                'notes' => "Transfer Produk {$sourceBox->product_id} ({$qtyInput} {$data['unit']}) dari Palet {$sourcePallet->id} ke Palet {$targetPallet->id}",
            ]);

            AuditLogger::log($userId, 'transfer', 'MOVE', "Transfer Antar Palet: {$qtyInput} {$data['unit']} Produk {$sourceBox->product_id} dari Palet {$sourcePallet->id} ke Palet {$targetPallet->id}");

            return [
                'source_box' => $sourceBox,
                'target_box' => $targetBox
            ];
        });
    }

    /**
     * KETENTUAN 7: Putaway Palet In Transit ke Rak Fisik
     */
    public function assignPalletToRack(string $palletId, string $binId, string $userId): Pallet
    {
        return DB::transaction(function () use ($palletId, $binId, $userId) {
            $bin = Bin::where('id', $binId)->lockForUpdate()->firstOrFail();

            $currentPalletCount = Pallet::where('bin_id', $bin->id)
                                        ->where('is_active', true)
                                        ->where('id', '!=', $palletId)
                                        ->count();

            if ($currentPalletCount >= $bin->max_pallet) {
                throw new Exception("Gagal: BIN {$bin->id} sudah penuh ({$currentPalletCount} Palet terdaftar). Maksimal {$bin->max_pallet} Palet per BIN.");
            }

            $pallet = Pallet::findOrFail($palletId);
            $pallet->bin_id = $bin->id;
            $pallet->status = 'on_rack';
            $pallet->is_active = true;
            $pallet->save();

            AuditLogger::log($userId, 'putaway', 'UPDATE', "Putaway Palet: Palet {$pallet->id} kini resmi ditempatkan pada BIN {$bin->id}");

            return $pallet;
        });
    }

    // --- LEGACY BACKWARD COMPATIBILITY METHODS ---
    public function processInbound(array $data, string $userId)
    {
        return $this->processInboundV2($data, $userId);
    }

    public function assignPalletToBin(string $binId, string $palletId, string $palletTypeId, string $userId)
    {
        return $this->assignPalletToRack($palletId, $binId, $userId);
    }

    public function attachBoxToPallet(array $data, ?string $userId = null)
    {
        $userId = $userId ?? auth()->id() ?? 'USR-001';
        return $this->processInboundV2($data, $userId);
    }

    public function processOutbound(array $data, ?string $userId = null)
    {
        $userId = $userId ?? auth()->id() ?? 'USR-001';
        $data['outbound_type'] = $data['outbound_type'] ?? 'ekspedisi';
        return $this->processOutboundV2($data, $userId);
    }

    public function processTransfer(string $palletId, string $targetBinId, string $userId)
    {
        return $this->assignPalletToRack($palletId, $targetBinId, $userId);
    }
}
