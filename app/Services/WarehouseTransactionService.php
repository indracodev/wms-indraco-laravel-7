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
     * Memproses Barang Masuk (Inbound Putaway) dengan validasi kapasitas BIN (Maksimal 2 Palet per BIN)
     */
    public function processInbound(array $data, string $userId)
    {
        return DB::transaction(function () use ($data, $userId) {
            // Lock record BIN untuk mencegah race condition
            $bin = Bin::where('id', $data['bin_id'])->lockForUpdate()->firstOrFail();

            // Hitung palet aktif saat ini di BIN tersebut
            $currentPalletCount = Pallet::where('bin_id', $bin->id)
                                        ->where('is_active', true)
                                        ->count();

            // Cek apakah penambahan palet baru melampaui limit max_pallet BIN
            $existingPallet = Pallet::find($data['pallet_id']);
            $isNewPalletInBin = !$existingPallet || $existingPallet->bin_id !== $bin->id;

            if ($isNewPalletInBin && $currentPalletCount >= $bin->max_pallet) {
                throw new Exception("Gagal: BIN {$bin->id} sudah penuh ({$currentPalletCount} Palet terdaftar). Maksimal {$bin->max_pallet} Palet per BIN.");
            }

            // Upsert Palet
            $pallet = Pallet::updateOrCreate(
                ['id' => $data['pallet_id']],
                [
                    'pallet_type_id' => $data['pallet_type_id'],
                    'bin_id' => $bin->id,
                    'is_active' => true
                ]
            );

            // Hitung Konversi UOM ke Satuan Terkecil (Base Unit)
            $qtyInput = (float) ($data['qty_input'] ?? $data['qty']);
            $uomInput = $data['unit'] ?? $data['uom_input'] ?? 'Box';
            $convInfo = $this->resolveConversion($data['product_id'], $uomInput);
            $qtyBase = $qtyInput * $convInfo['conversion_factor'];
            $uomBase = $convInfo['uom_base'];

            // Buat atau update Box
            $boxId = $data['box_id'] ?? ('BOX-' . strtoupper(substr(md5(uniqid()), 0, 6)));
            $box = Box::updateOrCreate(
                ['id' => $boxId],
                [
                    'pallet_id' => $pallet->id,
                    'product_id' => $data['product_id'],
                    'qty' => $qtyBase,
                    'uom_base' => $uomBase,
                    'initial_qty' => $qtyBase,
                    'expired_date' => $data['expired_date'],
                    'machine_code' => $data['machine_code'],
                    'status' => 'active',
                ]
            );

            // Dapatkan warehouse_id dari BIN -> Rack -> Warehouse
            $warehouseId = $data['warehouse_id'] ?? $bin->rack->warehouse_id;

            // Catat Log Transaksi Inbound Granular
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
                'machine_code' => $data['machine_code'],
                'bin_id' => $bin->id,
                'status' => 'In',
                'pallet_id' => $pallet->id,
                'pallet_type_id' => $data['pallet_type_id'],
                'box_id' => $box->id,
                'warehouse_id' => $warehouseId,
                'notes' => "Inbound {$qtyInput} {$uomInput} ({$qtyBase} {$uomBase}) ke Box {$box->id} di Palet {$pallet->id} BIN {$bin->id}",
            ]);

            // Audit Log
            AuditLogger::log($userId, 'inbound', 'CREATE', "Barang Masuk: {$qtyInput} {$uomInput} ({$qtyBase} {$uomBase}) Produk {$data['product_id']} ke Palet {$pallet->id} di BIN {$bin->id}");

            return $box;
        });
    }

    /**
     * Memproses Penempatan Palet ke BIN (Alur 1 Inbound)
     */
    public function assignPalletToBin(string $binId, string $palletId, string $palletTypeId, string $userId)
    {
        return DB::transaction(function () use ($binId, $palletId, $palletTypeId, $userId) {
            $bin = Bin::where('id', $binId)->lockForUpdate()->firstOrFail();

            $currentPalletCount = Pallet::where('bin_id', $bin->id)
                                        ->where('is_active', true)
                                        ->count();

            $existingPallet = Pallet::find($palletId);
            $isNewPalletInBin = !$existingPallet || $existingPallet->bin_id !== $bin->id;

            if ($isNewPalletInBin && $currentPalletCount >= $bin->max_pallet) {
                throw new Exception("Gagal: BIN {$bin->id} sudah penuh ({$currentPalletCount} Palet terdaftar). Maksimal {$bin->max_pallet} Palet per BIN.");
            }

            $pallet = Pallet::updateOrCreate(
                ['id' => $palletId],
                [
                    'pallet_type_id' => $palletTypeId,
                    'bin_id' => $bin->id,
                    'is_active' => true
                ]
            );

            AuditLogger::log($userId, 'inbound', 'UPDATE', "Penempatan Palet: Palet {$pallet->id} terdaftar di BIN {$bin->id}");

            return $pallet;
        });
    }

    /**
     * Memproses Pendataan Box ke Palet (Alur 2 Inbound)
     */
    public function attachBoxToPallet(array $data, ?string $userId = null)
    {
        $userId = $userId ?? auth()->id() ?? 'USR-001';
        return DB::transaction(function () use ($data, $userId) {
            $pallet = Pallet::where('id', $data['pallet_id'])->lockForUpdate()->firstOrFail();
            $bin = $pallet->bin;
            $binId = $bin ? $bin->id : 'TRANSIT';
            $warehouseId = $bin ? $bin->rack->warehouse_id : ($data['warehouse_id'] ?? 'GSK.678.I');

            // Hitung Konversi UOM
            $qtyInput = (float) ($data['qty_input'] ?? $data['qty']);
            $uomInput = $data['unit'] ?? $data['uom_input'] ?? 'Box';
            $convInfo = $this->resolveConversion($data['product_id'], $uomInput);
            $qtyBase = $qtyInput * $convInfo['conversion_factor'];
            $uomBase = $convInfo['uom_base'];

            $boxId = $data['box_id'] ?? ('BOX-' . strtoupper(substr(md5(uniqid()), 0, 6)));
            $box = Box::updateOrCreate(
                ['id' => $boxId],
                [
                    'pallet_id' => $pallet->id,
                    'product_id' => $data['product_id'],
                    'qty' => $qtyBase,
                    'uom_base' => $uomBase,
                    'initial_qty' => $qtyBase,
                    'expired_date' => $data['expired_date'],
                    'machine_code' => $data['machine_code'],
                    'status' => 'active',
                ]
            );

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
                'machine_code' => $data['machine_code'],
                'bin_id' => $binId,
                'status' => 'In',
                'pallet_id' => $pallet->id,
                'pallet_type_id' => $pallet->pallet_type_id,
                'box_id' => $box->id,
                'warehouse_id' => $warehouseId,
                'notes' => "Pendataan Box {$box->id}: {$qtyInput} {$uomInput} ({$qtyBase} {$uomBase}) pada Palet {$pallet->id} di BIN {$binId}",
            ]);

            AuditLogger::log($userId, 'inbound', 'CREATE', "Pendataan Box: Box {$box->id} ({$qtyInput} {$uomInput} = {$qtyBase} {$uomBase}) terdaftar pada Palet {$pallet->id} di BIN {$binId}");

            return $box;
        });
    }

    /**
     * Memproses Barang Keluar (Outbound Picking) Granular hingga Satuan Terkecil
     */
    public function processOutbound(array $data, ?string $userId = null)
    {
        $userId = $userId ?? auth()->id() ?? 'USR-001';
        return DB::transaction(function () use ($data, $userId) {
            $pallet = Pallet::where('id', $data['pallet_id'])->lockForUpdate()->firstOrFail();
            $binId = $pallet->bin_id ?? 'TRANSIT';
            $warehouseId = $pallet->bin ? $pallet->bin->rack->warehouse_id : 'GSK.678.I';

            $boxQuery = Box::where('pallet_id', $pallet->id)
                           ->where('product_id', $data['product_id'])
                           ->where('status', 'active');

            if (!empty($data['box_id'])) {
                $boxQuery->where('id', $data['box_id']);
            }

            $box = $boxQuery->lockForUpdate()->first();
            if (!$box) {
                throw new Exception("Gagal: Box aktif tidak ditemukan untuk produk {$data['product_id']} pada Palet {$pallet->id}.");
            }

            $qtyInput = (float) ($data['qty_input'] ?? $data['qty']);
            $uomInput = $data['unit'] ?? $data['uom_input'] ?? $box->uom_base;
            $convInfo = $this->resolveConversion($data['product_id'], $uomInput);
            $qtyBaseNeeded = $qtyInput * $convInfo['conversion_factor'];

            if ((float) $box->qty < $qtyBaseNeeded) {
                throw new Exception("Gagal: Qty pengambilan ({$qtyInput} {$uomInput} / {$qtyBaseNeeded} {$box->uom_base}) melebihi stok yang tersedia pada Box {$box->id} ({$box->qty} {$box->uom_base}).");
            }

            // Kurangi Qty Box dalam Satuan Terkecil (Base Unit)
            $box->qty = (float) $box->qty - $qtyBaseNeeded;
            if ($box->qty <= 0) {
                $box->qty = 0;
                $box->status = 'empty';
            }
            $box->save();

            // Cek apakah palet masih memiliki box aktif
            $activeBoxes = Box::where('pallet_id', $pallet->id)->where('status', 'active')->count();
            if ($activeBoxes === 0) {
                $pallet->is_active = false;
                $pallet->bin_id = null;
                $pallet->save();
            }

            // Catat Log Transaksi Outbound Granular
            TransactionLog::create([
                'pic_user_id' => $userId,
                'shift' => $data['shift'] ?? 1,
                'transaction_date' => now(),
                'product_id' => $data['product_id'],
                'qty' => $qtyBaseNeeded,
                'unit' => $uomInput,
                'uom_input' => $uomInput,
                'qty_input' => $qtyInput,
                'conversion_factor' => $convInfo['conversion_factor'],
                'uom_base' => $box->uom_base,
                'qty_base' => $qtyBaseNeeded,
                'expired_date' => $box->expired_date ?? null,
                'machine_code' => $box->machine_code ?? null,
                'bin_id' => $binId,
                'status' => 'Out',
                'pallet_id' => $pallet->id,
                'pallet_type_id' => $pallet->pallet_type_id,
                'box_id' => $box->id,
                'warehouse_id' => $warehouseId,
                'notes' => "Outbound {$qtyInput} {$uomInput} ({$qtyBaseNeeded} {$box->uom_base}) dari Box {$box->id} pada Palet {$pallet->id} di BIN {$binId}",
            ]);

            // Audit Log
            AuditLogger::log($userId, 'outbound', 'DELETE', "Barang Keluar: {$qtyInput} {$uomInput} ({$qtyBaseNeeded} {$box->uom_base}) Produk {$data['product_id']} dari Box {$box->id} Palet {$pallet->id} di BIN {$binId}");

            return true;
        });
    }

    /**
     * Memproses Transfer / Pemindahan Palet antar BIN dengan penguncian kapasitas 2 palet
     */
    public function processTransfer(string $palletId, string $targetBinId, string $userId)
    {
        return DB::transaction(function () use ($palletId, $targetBinId, $userId) {
            $targetBin = Bin::where('id', $targetBinId)->lockForUpdate()->firstOrFail();

            // Cek Kapasitas BIN Tujuan
            $currentPalletCount = Pallet::where('bin_id', $targetBin->id)
                                        ->where('is_active', true)
                                        ->count();

            if ($currentPalletCount >= $targetBin->max_pallet) {
                throw new Exception("Gagal: BIN Tujuan {$targetBin->id} sudah penuh ({$currentPalletCount} Palet terdaftar). Maksimal {$targetBin->max_pallet} Palet per BIN.");
            }

            $pallet = Pallet::findOrFail($palletId);
            $originBinId = $pallet->bin_id;

            // Update Lokasi Palet
            $pallet->bin_id = $targetBin->id;
            $pallet->is_active = true;
            $pallet->save();

            // Audit Log
            AuditLogger::log($userId, 'transfer', 'MOVE', "Memindahkan Palet {$palletId} dari BIN {$originBinId} ke BIN {$targetBin->id}");

            return $pallet;
        });
    }
}
