<?php

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductUomConversion;
use Illuminate\Support\Facades\DB;

class ProductMasterSeeder extends Seeder
{
    public function run()
    {
        $filePath = base_path('Master Item WMS.xlsx');
        if (!file_exists($filePath)) {
            $filePath = 'C:\\laragon\\www\\WMS Indraco\\Master Item WMS.xlsx';
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

        // Grouping data by Kode Item (Col A)
        $productsData = [];
        foreach ($rows as $r) {
            $kodeItem = $r['A'] ?? '';
            if (empty($kodeItem)) continue;

            $namaItem = $r['B'] ?? '';
            $groupItem = $r['C'] ?? null;
            $uomInventory = $r['D'] ?? 'Box';
            $grupUom = $r['E'] ?? null;
            $isiPerGroup = isset($r['F']) && is_numeric($r['F']) ? (float)$r['F'] : 1;
            $brand = $r['G'] ?? null;
            $activeRaw = strtolower($r['H'] ?? 'yes');
            $isActive = in_array($activeRaw, ['yes', 'ya', '1', 'true', 'active']);

            if (!isset($productsData[$kodeItem])) {
                $productsData[$kodeItem] = [
                    'id' => $kodeItem,
                    'name' => $namaItem,
                    'item_group' => $groupItem,
                    'uom_inventory' => $uomInventory,
                    'uom_group' => $grupUom,
                    'qty_per_uom_group' => $isiPerGroup,
                    'brand' => $brand,
                    'is_active' => $isActive,
                    'conversions' => []
                ];
            }

            $uomConvert = $r['I'] ?? '';
            $qtyConvert = isset($r['J']) && is_numeric($r['J']) ? (float)$r['J'] : 1;
            $uomBase = $r['K'] ?? '';

            if (!empty($uomConvert)) {
                $productsData[$kodeItem]['conversions'][] = [
                    'uom_convert' => $uomConvert,
                    'qty_convert' => $qtyConvert,
                    'uom_base' => !empty($uomBase) ? $uomBase : $uomInventory,
                ];
            }
        }

        DB::transaction(function () use ($productsData) {
            foreach ($productsData as $kodeItem => $p) {
                $product = Product::updateOrCreate(
                    ['id' => $kodeItem],
                    [
                        'name' => $p['name'],
                        'item_group' => $p['item_group'],
                        'uom_inventory' => $p['uom_inventory'],
                        'uom_group' => $p['uom_group'],
                        'qty_per_uom_group' => $p['qty_per_uom_group'],
                        'brand' => $p['brand'],
                        'is_active' => $p['is_active'],
                    ]
                );

                // Re-seed conversions
                ProductUomConversion::where('product_id', $kodeItem)->delete();
                foreach ($p['conversions'] as $conv) {
                    ProductUomConversion::create([
                        'product_id' => $kodeItem,
                        'uom_convert' => $conv['uom_convert'],
                        'qty_convert' => $conv['qty_convert'],
                        'uom_base' => $conv['uom_base'],
                    ]);
                }
            }
        });

        $this->command->info("Berhasil mengimpor " . count($productsData) . " produk dari Excel.");
    }
}
