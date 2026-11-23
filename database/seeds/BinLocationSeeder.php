<?php

use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\Rack;
use App\Models\Bin;
use App\Models\Branch;
use App\Models\Pallet;
use App\Models\TransactionLog;
use Illuminate\Support\Facades\DB;

class BinLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengacu pada:
     * 1. C:\laragon\www\#Project2026\wms-indraco\document\List Rak Gudang Finished Good.xlsx
     * 2. C:\laragon\www\#Project2026\wms-indraco\document\BIN Location Indraco (1).xlsx
     *
     * Total Bins: 7,714
     * - Gudang I (GSK.678.I): 3,172 bins (Rak A-J @ 280 bins, Rak K-L @ 186 bins)
     * - Gudang J (GSK.678.J): 1,470 bins (Rak A-G @ 210 bins)
     * - Gudang K (GSK.678.K): 3,072 bins (Rak A-H @ 384 bins)
     * Kapasitas Beam: 230 cm, 1 Palet per Beam.
     */
    public function run()
    {
        $this->command->info("Memulai Seeding BIN Location dari Peta Data & List Rak Gudang Finished Good...");

        // 1. Pastikan Branch Utama Tersedia
        $branch = Branch::firstOrCreate(
            ['id' => 'CBG-GSK'],
            ['office_id' => 'KTR-SBY', 'name' => 'Cabang Gresik', 'city' => 'Gresik']
        );

        // 2. Definisi 3 Gudang dari Peta Data
        $warehouses = [
            [
                'id' => 'GSK.678.I',
                'branch_id' => $branch->id,
                'name' => 'Gudang Bahan Baku & Jadi I G2 Gresik',
                'address' => 'Kawasan Industri Gresik Blok A-12',
                'prefix' => 'I',
                'racks' => [
                    ['name' => 'A', 'levels' => 4, 'cols' => 70],
                    ['name' => 'B', 'levels' => 4, 'cols' => 70],
                    ['name' => 'C', 'levels' => 4, 'cols' => 70],
                    ['name' => 'D', 'levels' => 4, 'cols' => 70],
                    ['name' => 'E', 'levels' => 4, 'cols' => 70],
                    ['name' => 'F', 'levels' => 4, 'cols' => 70],
                    ['name' => 'G', 'levels' => 4, 'cols' => 70],
                    ['name' => 'H', 'levels' => 4, 'cols' => 70],
                    ['name' => 'I', 'levels' => 4, 'cols' => 70],
                    ['name' => 'J', 'levels' => 4, 'cols' => 70],
                    ['name' => 'K', 'levels' => 3, 'cols' => 62],
                    ['name' => 'L', 'levels' => 3, 'cols' => 62],
                ]
            ],
            [
                'id' => 'GSK.678.J',
                'branch_id' => $branch->id,
                'name' => 'Gudang Bahan Baku & Jadi J G2 Gresik',
                'address' => 'Kawasan Industri Gresik Blok A-13',
                'prefix' => 'J',
                'racks' => [
                    ['name' => 'A', 'levels' => 5, 'cols' => 42],
                    ['name' => 'B', 'levels' => 5, 'cols' => 42],
                    ['name' => 'C', 'levels' => 5, 'cols' => 42],
                    ['name' => 'D', 'levels' => 5, 'cols' => 42],
                    ['name' => 'E', 'levels' => 5, 'cols' => 42],
                    ['name' => 'F', 'levels' => 5, 'cols' => 42],
                    ['name' => 'G', 'levels' => 5, 'cols' => 42],
                ]
            ],
            [
                'id' => 'GSK.678.K',
                'branch_id' => $branch->id,
                'name' => 'Gudang Bahan Baku & Jadi K G2 Gresik',
                'address' => 'Kawasan Industri Gresik Blok A-14',
                'prefix' => 'K',
                'racks' => [
                    ['name' => 'A', 'levels' => 6, 'cols' => 64],
                    ['name' => 'B', 'levels' => 6, 'cols' => 64],
                    ['name' => 'C', 'levels' => 6, 'cols' => 64],
                    ['name' => 'D', 'levels' => 6, 'cols' => 64],
                    ['name' => 'E', 'levels' => 6, 'cols' => 64],
                    ['name' => 'F', 'levels' => 6, 'cols' => 64],
                    ['name' => 'G', 'levels' => 6, 'cols' => 64],
                    ['name' => 'H', 'levels' => 6, 'cols' => 64],
                ]
            ],
        ];

        // Seed Warehouses & Racks
        foreach ($warehouses as $wh) {
            Warehouse::updateOrCreate(
                ['id' => $wh['id']],
                [
                    'branch_id' => $wh['branch_id'],
                    'name' => $wh['name'],
                    'address' => $wh['address']
                ]
            );

            foreach ($wh['racks'] as $rk) {
                Rack::updateOrCreate(
                    ['id' => "RK-{$wh['prefix']}-{$rk['name']}"],
                    [
                        'warehouse_id' => $wh['id'],
                        'rack_name' => $rk['name'],
                    ]
                );
            }
        }

        // 3. Baca List Rak Gudang Finished Good.xlsx
        $fileListRak = base_path('document/List Rak Gudang Finished Good.xlsx');
        if (!file_exists($fileListRak)) {
            $fileListRak = 'C:\\laragon\\www\\#Project2026\\wms-indraco\\document\\List Rak Gudang Finished Good.xlsx';
        }
        if (!file_exists($fileListRak)) {
            $fileListRak = 'C:\\laragon\\www\\WMS Indraco\\document\\List Rak Gudang Finished Good.xlsx';
        }

        $allBins = [];
        $parsedFromExcel = false;

        if (file_exists($fileListRak)) {
            $this->command->info("Membaca file List Rak: {$fileListRak}");
            $zip = new \ZipArchive();
            if ($zip->open($fileListRak) === true) {
                // Shared strings
                $strings = [];
                if (($sIndex = $zip->locateName('xl/sharedStrings.xml')) !== false) {
                    $sXml = simplexml_load_string($zip->getFromIndex($sIndex));
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

                // Sheet 1 is List Kode Beam
                if (($xIndex = $zip->locateName('xl/worksheets/sheet1.xml')) !== false) {
                    $sheetXml = simplexml_load_string($zip->getFromIndex($xIndex));
                    $rowNum = 0;
                    foreach ($sheetXml->sheetData->row as $row) {
                        $rowNum++;
                        if ($rowNum <= 1) continue; // Skip header

                        $rData = [];
                        foreach ($row->c as $c) {
                            $ref = (string)$c['r'];
                            $col = preg_replace('/[0-9]/', '', $ref);
                            $t = (string)$c['t'];
                            $val = (string)$c->v;
                            if ($t === 's' && $val !== '') {
                                $val = $strings[(int)$val] ?? '';
                            }
                            $rData[$col] = trim($val);
                        }

                        // C: Gedung, D: Rak, E: Baris, F: Kolom, G: Kode Beam, H: Panjang Beam, I: Isi Palet
                        $gedung = strtoupper($rData['C'] ?? '');
                        $rak = strtoupper($rData['D'] ?? '');
                        $baris = isset($rData['E']) && is_numeric($rData['E']) ? (int)$rData['E'] : 1;
                        $kolom = isset($rData['F']) && is_numeric($rData['F']) ? (int)$rData['F'] : 1;
                        $kodeBeam = $rData['G'] ?? "{$gedung} {$rak}.{$baris}.{$kolom}";
                        $panjangBeam = isset($rData['H']) && is_numeric($rData['H']) ? (int)$rData['H'] : 230;
                        $isiPalet = isset($rData['I']) && is_numeric($rData['I']) ? (int)$rData['I'] : 1;

                        if (!empty($gedung) && !empty($rak)) {
                            $allBins[$kodeBeam] = [
                                'id' => $kodeBeam,
                                'rack_id' => "RK-{$gedung}-{$rak}",
                                'column_no' => $kolom,
                                'level_no' => $baris,
                                'max_pallet' => $isiPalet,
                                'beam_length' => $panjangBeam,
                                'created_at' => now()->toDateTimeString(),
                                'updated_at' => now()->toDateTimeString(),
                            ];
                        }
                    }
                    $this->command->info("Parsed " . count($allBins) . " bins dari List Rak Gudang Finished Good.xlsx.");
                    $parsedFromExcel = true;
                }
                $zip->close();
            }
        }

        // Fallback jika file excel belum terbaca
        if (!$parsedFromExcel || count($allBins) === 0) {
            $this->command->warn("Menggunakan generator matriks layout peta data resmi...");
            foreach ($warehouses as $wh) {
                foreach ($wh['racks'] as $rk) {
                    for ($lvl = 1; $lvl <= $rk['levels']; $lvl++) {
                        for ($col = 1; $col <= $rk['cols']; $col++) {
                            $binId = "{$wh['prefix']} {$rk['name']}.{$lvl}.{$col}";
                            $allBins[$binId] = [
                                'id' => $binId,
                                'rack_id' => "RK-{$wh['prefix']}-{$rk['name']}",
                                'column_no' => $col,
                                'level_no' => $lvl,
                                'max_pallet' => 1,
                                'beam_length' => 230,
                                'created_at' => now()->toDateTimeString(),
                                'updated_at' => now()->toDateTimeString(),
                            ];
                        }
                    }
                }
            }
        }

        // 4. Update / Bulk Insert Bins dengan ON DUPLICATE KEY UPDATE
        $this->command->info("Menyimpan atau memperbarui data " . count($allBins) . " BIN ke database...");

        $binChunks = array_chunk(array_values($allBins), 500);
        foreach ($binChunks as $chunk) {
            $valuePlaceholders = [];
            $bindings = [];
            foreach ($chunk as $b) {
                $valuePlaceholders[] = "(?, ?, ?, ?, ?, ?, ?, ?)";
                $bindings[] = $b['id'];
                $bindings[] = $b['rack_id'];
                $bindings[] = $b['column_no'];
                $bindings[] = $b['level_no'];
                $bindings[] = $b['max_pallet'];
                $bindings[] = $b['beam_length'];
                $bindings[] = $b['created_at'];
                $bindings[] = $b['updated_at'];
            }

            $sql = "INSERT INTO bins (id, rack_id, column_no, level_no, max_pallet, beam_length, created_at, updated_at) 
                    VALUES " . implode(', ', $valuePlaceholders) . " 
                    ON DUPLICATE KEY UPDATE 
                    rack_id = VALUES(rack_id),
                    column_no = VALUES(column_no),
                    level_no = VALUES(level_no),
                    max_pallet = VALUES(max_pallet), 
                    beam_length = VALUES(beam_length), 
                    updated_at = VALUES(updated_at)";
            DB::statement($sql, $bindings);
        }

        // 5. Sesuaikan Palet Demo agar mematuhi aturan 1 palet per beam (max_pallet = 1)
        $this->command->info("Menyesuaikan alokasi palet demo untuk batas 1 palet per beam...");
        $palletRelocations = [
            'PLT-0001' => 'I B.2.10',
            'PLT-0002' => 'I B.1.10',
            'PLT-0003' => 'I C.1.1',
            'PLT-6666' => 'I A.1.1',
            'PLT-8524' => 'I A.2.1',
        ];

        foreach ($palletRelocations as $pltId => $binId) {
            Pallet::where('id', $pltId)->update(['bin_id' => $binId]);
            TransactionLog::where('pallet_id', $pltId)->update(['bin_id' => $binId]);
        }

        $totalInDb = Bin::count();
        $this->command->info("Selesai! Total BIN di database: {$totalInDb} BIN.");
    }
}
