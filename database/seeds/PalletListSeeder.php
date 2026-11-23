<?php

use Illuminate\Database\Seeder;
use App\Models\Pallet;
use App\Models\PalletType;
use Illuminate\Support\Facades\DB;

class PalletListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengacu pada: List Palet Gudang.xlsx (11.000 data palet)
     */
    public function run()
    {
        $possiblePaths = [
            base_path('List Palet Gudang.xlsx'),
            base_path('document/List Palet Gudang.xlsx'),
            'C:\\laragon\\www\\#Project2026\\wms-indraco\\List Palet Gudang.xlsx',
            'C:\\laragon\\www\\WMS Indraco\\List Palet Gudang.xlsx',
        ];

        $filePath = null;
        foreach ($possiblePaths as $p) {
            if (file_exists($p)) {
                $filePath = $p;
                break;
            }
        }

        if (!$filePath) {
            $this->command->error("File Excel List Palet Gudang.xlsx tidak ditemukan.");
            return;
        }

        $this->command->info("Memproses seeder palet dari file: {$filePath}");

        // 1. Pastikan seluruh Master Tipe Palet tersedia
        $palletTypes = [
            ['id' => 'plastik', 'name' => 'Palet Plastik'],
            ['id' => 'loscam', 'name' => 'Palet Loscam'],
            ['id' => 'kayu_135', 'name' => 'Palet Kayu 135x125cm'],
            ['id' => 'kayu_125', 'name' => 'Palet Kayu Standar (125x125cm)'],
        ];

        foreach ($palletTypes as $pt) {
            PalletType::updateOrCreate(['id' => $pt['id']], $pt);
        }
        $this->command->info("Master Pallet Types berhasil disinkronkan.");

        // 2. Pemetaan nama palet Excel ke ID PalletType
        $typeMapping = [
            'PLASTIK' => 'plastik',
            'LOSCAM' => 'loscam',
            'KAYU 135X125' => 'kayu_135',
            'KAYU 125X125' => 'kayu_125',
        ];

        // 3. Ekstraksi data dari file Excel XLSX via ZipArchive & SimpleXML
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            $this->command->error("Gagal membuka file Excel: {$filePath}");
            return;
        }

        // Shared strings
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

        // Baca worksheet pertama (sheet1.xml atau sheet 'List Palet')
        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetXmlContent) {
            // Cari file sheet dari workbook.xml jika sheet1 tidak langsung cocok
            if (($wbIdx = $zip->locateName('xl/workbook.xml')) !== false) {
                $wbXml = simplexml_load_string($zip->getFromIndex($wbIdx));
                foreach ($wbXml->sheets->sheet as $sh) {
                    $shName = (string)$sh['name'];
                    if (stripos($shName, 'Palet') !== false) {
                        $rId = (string)$sh->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                        if (($relsIdx = $zip->locateName('xl/_rels/workbook.xml.rels')) !== false) {
                            $relsXml = simplexml_load_string($zip->getFromIndex($relsIdx));
                            foreach ($relsXml->Relationship as $rel) {
                                if ((string)$rel['Id'] === $rId) {
                                    $target = 'xl/' . (string)$rel['Target'];
                                    $sheetXmlContent = $zip->getFromName($target);
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!$sheetXmlContent) {
            $this->command->error("Worksheet data palet tidak ditemukan dalam XLSX.");
            $zip->close();
            return;
        }

        $sheetXml = simplexml_load_string($sheetXmlContent);
        $zip->close();

        $rowsToInsert = [];
        $now = now();
        $totalParsed = 0;

        foreach ($sheetXml->sheetData->row as $row) {
            $totalParsed++;
            if ($totalParsed === 1) {
                continue; // Lewati header kolom (Nomor, Nama Palet, Kode Palet)
            }

            $colVals = [];
            foreach ($row->c as $c) {
                $ref = (string)$c['r'];
                $col = preg_replace('/[0-9]/', '', $ref);
                $t = (string)$c['t'];
                $val = (string)$c->v;
                if ($t === 's' && $val !== '') {
                    $val = $strings[(int)$val] ?? '';
                }
                $colVals[$col] = trim($val);
            }

            $namaPalet = strtoupper(trim($colVals['B'] ?? ''));
            $kodePalet = strtoupper(trim($colVals['C'] ?? ''));

            if (empty($kodePalet)) {
                continue;
            }

            $typeId = $typeMapping[$namaPalet] ?? 'loscam';

            $rowsToInsert[] = [
                'id' => $kodePalet,
                'pallet_type_id' => $typeId,
                'bin_id' => null,
                'status' => 'in_transit',
                'max_capacity' => 72,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $totalPallets = count($rowsToInsert);
        $this->command->info("Berhasil membaca {$totalPallets} baris data palet dari Excel.");

        // 4. Masukkan ke database secara bertahap (chunking 1000)
        $chunkSize = 1000;
        $insertedCount = 0;

        foreach (array_chunk($rowsToInsert, $chunkSize) as $chunk) {
            DB::table('pallets')->insertOrIgnore($chunk);
            $insertedCount += count($chunk);
            $this->command->line("Progress seeder: {$insertedCount} / {$totalPallets} palet telah diproses...");
        }

        $this->command->info("Seeder selesai! Total {$totalPallets} palet berhasil disinkronkan ke database.");
    }
}
