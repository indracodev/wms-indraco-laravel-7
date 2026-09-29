<?php

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductUomConversion;
use Illuminate\Support\Facades\DB;

class ProductMasterSeeder extends Seeder
{
    public function run()
    {
        $filePath = base_path('document/MASTER DATA ITEM.xlsx');
        if (!file_exists($filePath)) {
            $filePath = 'C:\\laragon\\www\\WMS Indraco\\document\\MASTER DATA ITEM.xlsx';
        }
        if (!file_exists($filePath)) {
            $filePath = base_path('Master Item WMS.xlsx');
        }

        if (!file_exists($filePath)) {
            $this->command->error("File Excel tidak ditemukan di: {$filePath}");
            return;
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            $this->command->error("Gagal membuka file XLSX: {$filePath}");
            return;
        }

        // 1. Read Shared Strings
        $strings = [];
        if (($index = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $xml = simplexml_load_string($zip->getFromIndex($index));
            foreach ($xml->si as $si) {
                if (isset($si->t)) {
                    $strings[] = (string)$si->t;
                } else {
                    $t = '';
                    foreach ($si->r as $r) {
                        $t .= (string)$r->t;
                    }
                    $strings[] = $t;
                }
            }
        }

        // 2. Read Sheet Data
        $rows = [];
        if (($index = $zip->locateName('xl/worksheets/sheet1.xml')) !== false) {
            $xml = simplexml_load_string($zip->getFromIndex($index));
            foreach ($xml->sheetData->row as $row) {
                $rowData = [];
                foreach ($row->c as $c) {
                    $ref = (string)$c['r'];
                    $col = preg_replace('/[0-9]/', '', $ref);
                    $t = (string)$c['t'];
                    $val = (string)$c->v;
                    if ($t === 's' && $val !== '') {
                        $val = $strings[(int)$val] ?? '';
                    }
                    $rowData[$col] = trim($val);
                }
                $rows[] = $rowData;
            }
        }
        $zip->close();

        if (count($rows) <= 1) {
            $this->command->warn("Tidak ada data dalam file Excel.");
            return;
        }

        // Remove header row
        $header = array_shift($rows);

        $productsData = [];
        foreach ($rows as $r) {
            $kodeItem = $r['A'] ?? '';
            if (empty($kodeItem)) continue;

            $namaItem = $r['B'] ?? '';
            $groupItem = $r['D'] ?? 'FINISHED GOODS (MANUFAKTUR)';
            
            $uom1 = !empty($r['H']) ? $r['H'] : null;
            $uom2 = !empty($r['J']) ? $r['J'] : null;
            $uom3 = !empty($r['L']) ? $r['L'] : 'Box'; // Base UOM
            
            $qtyUom1 = isset($r['G']) && is_numeric($r['G']) ? (float)$r['G'] : 1;
            $qtyUom2 = isset($r['I']) && is_numeric($r['I']) ? (float)$r['I'] : null;
            $qtyUom3 = isset($r['K']) && is_numeric($r['K']) ? (float)$r['K'] : 1;
            
            $kartonToPcs = $r['M'] ?? null;
            $uomGroup = $r['N'] ?? null;
            $noOfUnits = isset($r['O']) && is_numeric($r['O']) ? (int)$r['O'] : 1;
            
            $salesTax = $r['P'] ?? 'PPN Out 10%';
            $purchaseTax = $r['Q'] ?? 'PPN In 11%';
            
            $purchaseItem = strtolower($r['R'] ?? 'no') === 'yes';
            $salesItem = strtolower($r['S'] ?? 'yes') !== 'no';
            $inventoryItem = strtolower($r['T'] ?? 'yes') !== 'no';
            $manageBatchNo = strtolower($r['U'] ?? 'yes') !== 'no';
            
            $groupLevel2 = $r['V'] ?? null;
            $groupLevel3 = $r['W'] ?? null;
            $brand = $r['X'] ?? null;
            
            $weightNetG = isset($r['Z']) && is_numeric($r['Z']) ? (float)$r['Z'] : 0;
            $activeRaw = strtolower($r['AA'] ?? 'yes');
            $isActive = in_array($activeRaw, ['yes', 'ya', '1', 'true', 'active']);
            
            $lengthCm = isset($r['AE']) && is_numeric($r['AE']) ? (float)$r['AE'] : 0;
            $widthCm = isset($r['AF']) && is_numeric($r['AF']) ? (float)$r['AF'] : 0;
            $heightCm = isset($r['AG']) && is_numeric($r['AG']) ? (float)$r['AG'] : 0;
            $volumeM3 = isset($r['AH']) && is_numeric($r['AH']) ? (float)$r['AH'] : 0;
            $weightGrossKg = isset($r['AI']) && is_numeric($r['AI']) ? (float)$r['AI'] : 0;

            $isManualUom = ($uomGroup === 'Manual' || strtolower((string)$kartonToPcs) === 'manual');

            // Calculate numeric Karton-to-PCS factor
            $kartonToPcsNumeric = is_numeric($kartonToPcs) ? (float)$kartonToPcs : 1;

            if (!isset($productsData[$kodeItem])) {
                $productsData[$kodeItem] = [
                    'id' => $kodeItem,
                    'name' => $namaItem,
                    'item_group' => $groupItem,
                    'group_level_2' => $groupLevel2,
                    'group_level_3' => $groupLevel3,
                    'brand' => $brand,
                    'uom_inventory' => $uom3,
                    'uom_group' => $uomGroup,
                    'uom_1' => $uom1,
                    'uom_2' => $uom2,
                    'uom_3' => $uom3,
                    'karton_to_pcs' => $kartonToPcs,
                    'no_of_units' => $noOfUnits,
                    'qty_per_uom_group' => $kartonToPcsNumeric,
                    'is_manual_uom' => $isManualUom,
                    'manage_batch_no' => $manageBatchNo,
                    'purchase_item' => $purchaseItem,
                    'sales_item' => $salesItem,
                    'inventory_item' => $inventoryItem,
                    'sales_tax_definition' => $salesTax,
                    'purchase_tax_definition' => $purchaseTax,
                    'is_active' => $isActive,
                    'length_cm' => $lengthCm,
                    'width_cm' => $widthCm,
                    'height_cm' => $heightCm,
                    'volume_m3' => $volumeM3,
                    'weight_gross_kg' => $weightGrossKg,
                    'weight_net_g' => $weightNetG,
                    'conversions' => []
                ];

                // Level 1: Outer UOM (Karton) to Base UOM
                $outerUomName = !empty($uom1) ? $uom1 : 'Karton';
                $productsData[$kodeItem]['conversions'][] = [
                    'level' => 1,
                    'uom_convert' => $outerUomName,
                    'qty_convert' => $kartonToPcsNumeric,
                    'uom_base' => $uom3,
                    'multiplier_to_base' => $kartonToPcsNumeric,
                ];

                // Level 2: Intermediate UOM (Renceng/BoxBNS) to Base UOM (if present)
                if (!empty($uom2) && $qtyUom3 > 0) {
                    $productsData[$kodeItem]['conversions'][] = [
                        'level' => 2,
                        'uom_convert' => $uom2,
                        'qty_convert' => $qtyUom3,
                        'uom_base' => $uom3,
                        'multiplier_to_base' => $qtyUom3,
                    ];
                }

                // Level 3: Base UOM (1:1)
                if (strtoupper($outerUomName) !== strtoupper($uom3) && (empty($uom2) || strtoupper($uom2) !== strtoupper($uom3))) {
                    $productsData[$kodeItem]['conversions'][] = [
                        'level' => 3,
                        'uom_convert' => $uom3,
                        'qty_convert' => 1,
                        'uom_base' => $uom3,
                        'multiplier_to_base' => 1,
                    ];
                }
            }
        }

        DB::transaction(function () use ($productsData) {
            foreach ($productsData as $kodeItem => $p) {
                $product = Product::updateOrCreate(
                    ['id' => $kodeItem],
                    [
                        'name' => $p['name'],
                        'item_group' => $p['item_group'],
                        'group_level_2' => $p['group_level_2'],
                        'group_level_3' => $p['group_level_3'],
                        'brand' => $p['brand'],
                        'uom_inventory' => $p['uom_inventory'],
                        'uom_group' => $p['uom_group'],
                        'uom_1' => $p['uom_1'],
                        'uom_2' => $p['uom_2'],
                        'uom_3' => $p['uom_3'],
                        'karton_to_pcs' => $p['karton_to_pcs'],
                        'no_of_units' => $p['no_of_units'],
                        'qty_per_uom_group' => $p['qty_per_uom_group'],
                        'is_manual_uom' => $p['is_manual_uom'],
                        'manage_batch_no' => $p['manage_batch_no'],
                        'purchase_item' => $p['purchase_item'],
                        'sales_item' => $p['sales_item'],
                        'inventory_item' => $p['inventory_item'],
                        'sales_tax_definition' => $p['sales_tax_definition'],
                        'purchase_tax_definition' => $p['purchase_tax_definition'],
                        'is_active' => $p['is_active'],
                        'length_cm' => $p['length_cm'],
                        'width_cm' => $p['width_cm'],
                        'height_cm' => $p['height_cm'],
                        'volume_m3' => $p['volume_m3'],
                        'weight_gross_kg' => $p['weight_gross_kg'],
                        'weight_net_g' => $p['weight_net_g'],
                    ]
                );

                // Re-seed conversions
                ProductUomConversion::where('product_id', $kodeItem)->delete();
                foreach ($p['conversions'] as $conv) {
                    ProductUomConversion::create([
                        'product_id' => $kodeItem,
                        'level' => $conv['level'],
                        'uom_convert' => $conv['uom_convert'],
                        'qty_convert' => $conv['qty_convert'],
                        'uom_base' => $conv['uom_base'],
                        'multiplier_to_base' => $conv['multiplier_to_base'],
                    ]);
                }
            }
        });

        $this->command->info("Berhasil mengimpor " . count($productsData) . " produk dengan 35 kolom & UOM bertingkat dari Excel.");
    }
}
