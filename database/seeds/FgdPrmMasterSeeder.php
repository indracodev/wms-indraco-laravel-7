<?php

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductUomConversion;
use Illuminate\Support\Facades\DB;

class FgdPrmMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengacu pada: C:\laragon\www\#Project2026\wms-indraco\document\MASTER DATA FGD + PRM.xlsx
     * Sesuai instruksi: kolom kubikasi (panjang x lebar x tinggi, volume) dan barcode dikosongkan (0 / null).
     */
    public function run()
    {
        $filePath = base_path('document/MASTER DATA FGD + PRM.xlsx');
        if (!file_exists($filePath)) {
            $filePath = 'C:\\laragon\\www\\#Project2026\\wms-indraco\\document\\MASTER DATA FGD + PRM.xlsx';
        }
        if (!file_exists($filePath)) {
            $filePath = 'C:\\laragon\\www\\WMS Indraco\\document\\MASTER DATA FGD + PRM.xlsx';
        }

        if (!file_exists($filePath)) {
            $this->command->error("File Excel tidak ditemukan di: {$filePath}");
            return;
        }

        $this->command->info("Membuka file Excel Master Data: {$filePath}");

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            $this->command->error("Gagal membuka file XLSX: {$filePath}");
            return;
        }

        // 1. Read Shared Strings
        $strings = [];
        if (($sIdx = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $sXml = simplexml_load_string($zip->getFromIndex($sIdx));
            foreach ($sXml->si as $si) {
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

        // 2. Map Sheet Names to Sheet XML Files
        $relsMap = [];
        if (($relsIdx = $zip->locateName('xl/_rels/workbook.xml.rels')) !== false) {
            $relsXml = simplexml_load_string($zip->getFromIndex($relsIdx));
            foreach ($relsXml->Relationship as $rel) {
                $relsMap[(string)$rel['Id']] = basename((string)$rel['Target']);
            }
        }

        $sheetMap = [];
        if (($wbIdx = $zip->locateName('xl/workbook.xml')) !== false) {
            $wbXml = simplexml_load_string($zip->getFromIndex($wbIdx));
            foreach ($wbXml->sheets->sheet as $sh) {
                $name = (string)$sh['name'];
                $rId = (string)$sh->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                if (isset($relsMap[$rId])) {
                    $sheetMap[$name] = $relsMap[$rId];
                }
            }
        }

        // Column mapping according to standardized 20-column header:
        // A: Item No., B: Item Description, C: Inventory UoM, D: UoM Group
        // E: UoM KARTON, F: UoM BAG, G: UoM BOX, H: UoM CAN, I: UoM Kg, J: UoM PCS,
        // K: UoM SACHET, L: UoM SET, M: UoM BOXBNS, N: UoM RENTENG, O: UoM RENTENGBNS
        // P: Panjang, Q: Lebar, R: Tinggi, S: Berat, T: Barcode
        $colMap = [
            'A' => 'item_no',
            'B' => 'item_desc',
            'C' => 'inventory_uom',
            'D' => 'uom_group',
            'E' => 'Karton',
            'F' => 'Bag',
            'G' => 'Box',
            'H' => 'Can',
            'I' => 'Kg',
            'J' => 'Pcs',
            'K' => 'Sachet',
            'L' => 'Set',
            'M' => 'BoxBns',
            'N' => 'Renteng',
            'O' => 'RentengBns',
            'P' => 'panjang',
            'Q' => 'lebar',
            'R' => 'tinggi',
            'S' => 'berat',
            'T' => 'barcode',
        ];

        $uomColumns = ['Bag', 'Box', 'Can', 'Kg', 'Pcs', 'Sachet', 'Set', 'BoxBns', 'Renteng', 'RentengBns'];

        $productsData = [];
        $totalParsed = 0;

        foreach ($sheetMap as $sheetName => $xmlFile) {
            if (strtoupper($sheetName) === 'SUMMARY') {
                continue;
            }

            if (($shIdx = $zip->locateName("xl/worksheets/{$xmlFile}")) === false) {
                continue;
            }

            $isPrm = (strcasecmp($sheetName, 'Material Promosi') === 0);
            $itemGroup = $isPrm ? 'MATERIAL PROMOSI' : 'FINISHED GOODS';
            $brand = $isPrm ? 'Promosi' : $sheetName;

            $shXml = simplexml_load_string($zip->getFromIndex($shIdx));
            $rowNum = 0;

            foreach ($shXml->sheetData->row as $row) {
                $rowNum++;
                if ($rowNum <= 2) {
                    continue; // Skip title row & header row
                }

                $rowData = [];
                foreach ($row->c as $c) {
                    $ref = (string)$c['r'];
                    $col = preg_replace('/[0-9]/', '', $ref);
                    $t = (string)$c['t'];
                    $val = (string)$c->v;
                    if ($t === 's' && $val !== '') {
                        $val = $strings[(int)$val] ?? '';
                    }
                    if (isset($colMap[$col])) {
                        $rowData[$colMap[$col]] = trim($val);
                    }
                }

                $itemNo = $rowData['item_no'] ?? '';
                if (empty($itemNo)) {
                    continue;
                }

                $namaItem = $rowData['item_desc'] ?? '';
                $invUom = !empty($rowData['inventory_uom']) ? $rowData['inventory_uom'] : 'Pcs';
                $uomGroup = !empty($rowData['uom_group']) ? $rowData['uom_group'] : 'Manual';
                $isManual = (strcasecmp($uomGroup, 'Manual') === 0);

                // Collect nonzero UOM values
                $uomValues = [];
                foreach ($uomColumns as $uCol) {
                    if (isset($rowData[$uCol]) && is_numeric($rowData[$uCol])) {
                        $val = (float)$rowData[$uCol];
                        if ($val > 0) {
                            $uomValues[$uCol] = $val;
                        }
                    }
                }

                // Determine base quantity per karton
                $baseQty = null;
                $matchedBaseCol = null;
                foreach ($uomValues as $uCol => $val) {
                    if (strcasecmp($uCol, $invUom) === 0 || (in_array(strtolower($invUom), ['pieces', 'pcs']) && strtolower($uCol) === 'pcs')) {
                        $baseQty = $val;
                        $matchedBaseCol = $uCol;
                        break;
                    }
                }

                if ($baseQty === null) {
                    if (preg_match('/\d+/', $uomGroup, $m)) {
                        $baseQty = (float)$m[0];
                    } else {
                        $baseQty = 1.0;
                    }
                }

                // Intermediate UOM if any (Level 2)
                $intermediateUom = null;
                foreach ($uomValues as $uCol => $val) {
                    if ($matchedBaseCol && strcasecmp($uCol, $matchedBaseCol) === 0) {
                        continue;
                    }
                    $intermediateUom = $uCol;
                    break;
                }

                $weightGrossKg = isset($rowData['berat']) && is_numeric($rowData['berat']) ? (float)$rowData['berat'] : 0.00;

                // Build conversions
                $conversions = [];

                // Level 1: Karton to Base
                $conversions[] = [
                    'level' => 1,
                    'uom_convert' => 'Karton',
                    'qty_convert' => $baseQty,
                    'uom_base' => $invUom,
                    'multiplier_to_base' => $baseQty,
                ];

                // Level 2: Intermediates (e.g. Renteng, Bag, BoxBns) to Base
                foreach ($uomValues as $uCol => $val) {
                    if ($matchedBaseCol && strcasecmp($uCol, $matchedBaseCol) === 0) {
                        continue;
                    }
                    $mult = ($val > 0) ? round($baseQty / $val, 4) : 1.0;
                    $conversions[] = [
                        'level' => 2,
                        'uom_convert' => $uCol,
                        'qty_convert' => $val,
                        'uom_base' => $invUom,
                        'multiplier_to_base' => $mult,
                    ];
                }

                // Level 3: Base UOM (1:1)
                $conversions[] = [
                    'level' => 3,
                    'uom_convert' => $invUom,
                    'qty_convert' => 1.00,
                    'uom_base' => $invUom,
                    'multiplier_to_base' => 1.00,
                ];

                $productsData[$itemNo] = [
                    'id' => $itemNo,
                    'name' => $namaItem,
                    'item_group' => $itemGroup,
                    'group_level_2' => null,
                    'group_level_3' => null,
                    'brand' => $brand,
                    'uom_inventory' => $invUom,
                    'uom_group' => $uomGroup,
                    'uom_1' => 'Karton',
                    'uom_2' => $intermediateUom,
                    'uom_3' => $invUom,
                    'karton_to_pcs' => (string)$baseQty,
                    'no_of_units' => 1,
                    'qty_per_uom_group' => $baseQty,
                    'is_manual_uom' => $isManual,
                    'manage_batch_no' => !$isPrm,
                    'purchase_item' => false,
                    'sales_item' => true,
                    'inventory_item' => true,
                    'sales_tax_definition' => 'PPN Out 11%',
                    'purchase_tax_definition' => 'PPN In 11%',
                    'is_active' => true,
                    // Sesuai instruksi: kosongkan kolom kubikasi & barcode
                    'length_cm' => 0.00,
                    'width_cm' => 0.00,
                    'height_cm' => 0.00,
                    'volume_m3' => 0.0000,
                    'weight_gross_kg' => $weightGrossKg,
                    'weight_net_g' => 0.00,
                    'barcode' => null,
                    'conversions' => $conversions,
                ];

                $totalParsed++;
            }
        }

        $zip->close();

        $this->command->info("Parsed {$totalParsed} produk dari Excel. Menyimpan ke database...");

        // Insert or Update Database in Transaction
        DB::transaction(function () use ($productsData) {
            foreach ($productsData as $itemNo => $p) {
                Product::updateOrCreate(
                    ['id' => $itemNo],
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
                        'barcode' => $p['barcode'],
                    ]
                );

                // Re-seed UoM conversions
                ProductUomConversion::where('product_id', $itemNo)->delete();
                foreach ($p['conversions'] as $conv) {
                    ProductUomConversion::create([
                        'product_id' => $itemNo,
                        'level' => $conv['level'],
                        'uom_convert' => $conv['uom_convert'],
                        'qty_convert' => $conv['qty_convert'],
                        'uom_base' => $conv['uom_base'],
                        'multiplier_to_base' => $conv['multiplier_to_base'],
                    ]);
                }
            }
        });

        $this->command->info("Selesai! Berhasil mengimpor " . count($productsData) . " master produk FGD & PRM beserta konversi UoM ke database.");
    }
}
