<?php

use Illuminate\Database\Seeder;
use App\Models\Office;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\Rack;
use App\Models\Bin;
use App\Models\PalletType;
use App\Models\Uom;
use App\Models\Product;
use App\Models\Pallet;
use App\Models\Box;
use App\Models\TransactionLog;

require_once __DIR__ . '/BinLocationSeeder.php';
require_once __DIR__ . '/ProductMasterSeeder.php';
require_once __DIR__ . '/FgdPrmMasterSeeder.php';
require_once __DIR__ . '/PalletListSeeder.php';

class MasterDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Office
        $office = Office::updateOrCreate(
            ['id' => 'KTR-SBY'],
            ['name' => 'Kantor Pusat Surabaya', 'address' => 'Jl. Kombes Pol M. Duryat No. 25, Surabaya']
        );

        // 2. Branch
        $branch = Branch::updateOrCreate(
            ['id' => 'CBG-GSK'],
            ['office_id' => $office->id, 'name' => 'Cabang Gresik', 'city' => 'Gresik']
        );

        // 3. Warehouses, Racks & Bins (Mengacu pada List Rak Gudang Finished Good.xlsx)
        $this->call(BinLocationSeeder::class);

        // 4. Pallet Types
        $palletTypes = [
            ['id' => 'loscam', 'name' => 'Palet Loscam'],
            ['id' => 'kayu_125', 'name' => 'Palet Kayu Standar (125cm)'],
            ['id' => 'kayu_135', 'name' => 'Palet Kayu Ukuran 135x125cm'],
            ['id' => 'plastik', 'name' => 'Palet Plastik'],
        ];
        foreach ($palletTypes as $pt) {
            PalletType::updateOrCreate(['id' => $pt['id']], $pt);
        }

        // 4b. Seed 11.000 Master Pallets from List Palet Gudang.xlsx
        $this->call(PalletListSeeder::class);

        // 5. UOM Inventory (Base Unit & Conversions)
        $uoms = [
            ['id' => 'BOX', 'name' => 'Box', 'description' => 'Satuan kemasan kotak / dus kardus utama'],
            ['id' => 'PCS', 'name' => 'PCS', 'description' => 'Satuan pieces / satuan tunggal eceran'],
            ['id' => 'KG', 'name' => 'KG', 'description' => 'Satuan berat kilogram'],
            ['id' => 'SACHET', 'name' => 'Sachet', 'description' => 'Satuan kemasan sachet kecil'],
            ['id' => 'CAN', 'name' => 'Can', 'description' => 'Satuan kemasan kaleng'],
            ['id' => 'BAG', 'name' => 'Bag', 'description' => 'Satuan kemasan kantong / bag'],
            ['id' => 'KARTON', 'name' => 'Karton', 'description' => 'Satuan karton pengiriman'],
            ['id' => 'PIECES', 'name' => 'Pieces', 'description' => 'Satuan pieces produk'],
            ['id' => 'SET', 'name' => 'Set', 'description' => 'Satuan paket set produk'],
            ['id' => 'BOXBNS', 'name' => 'BoxBns', 'description' => 'Satuan box bonus / intermediate packaging'],
            ['id' => 'RENTENG', 'name' => 'Renteng', 'description' => 'Satuan renteng / renceng multi-sachet'],
            ['id' => 'RENTENGBNS', 'name' => 'RentengBns', 'description' => 'Satuan renteng bonus'],
        ];
        foreach ($uoms as $u) {
            Uom::updateOrCreate(['id' => $u['id']], $u);
        }

        // 6. Master Products FGD + PRM dari MASTER DATA FGD + PRM.xlsx
        $this->call(FgdPrmMasterSeeder::class);

        // 7. Pallets, Boxes & Transaction Logs Demo Seed
        $initialData = [
            [
                'pallet_id' => 'PLT-0001',
                'pallet_type_id' => 'loscam',
                'bin_id' => 'I B.2.10',
                'box_id' => 'BOX-0001',
                'product_id' => 'FGD00002', // BaliCafé Arabica Gold Drip 5 Sachets x 10g Box
                'qty' => 20, // 1 Karton = 20 Box
                'uom_base' => 'Box',
                'uom_input' => 'KARTON',
                'qty_input' => 1,
                'conversion_factor' => 20,
                'expired_date' => '2026-12-31',
                'machine_code' => 'MC-01',
            ],
            [
                'pallet_id' => 'PLT-0002',
                'pallet_type_id' => 'kayu_125',
                'bin_id' => 'I B.1.10',
                'box_id' => 'BOX-0002',
                'product_id' => 'FGD00024', // Bro Choco Banana P10
                'qty' => 10,
                'uom_base' => 'Bag',
                'uom_input' => 'KARTON',
                'qty_input' => 1,
                'conversion_factor' => 10,
                'expired_date' => '2026-10-15',
                'machine_code' => 'MC-02',
            ],
            [
                'pallet_id' => 'PLT-0003',
                'pallet_type_id' => 'plastik',
                'bin_id' => 'I C.1.1',
                'box_id' => 'BOX-0003',
                'product_id' => 'FGD00035', // Hao Cafe Cappuccino R10
                'qty' => 250,
                'uom_base' => 'Sachet',
                'uom_input' => 'KARTON',
                'qty_input' => 1,
                'conversion_factor' => 250,
                'expired_date' => '2026-08-20',
                'machine_code' => 'MC-03',
            ],
        ];

        foreach ($initialData as $item) {
            Pallet::updateOrCreate(
                ['id' => $item['pallet_id']],
                [
                    'pallet_type_id' => $item['pallet_type_id'],
                    'bin_id' => $item['bin_id'],
                    'is_active' => true
                ]
            );

            $box = Box::updateOrCreate(
                ['id' => $item['box_id']],
                [
                    'pallet_id' => $item['pallet_id'],
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'uom_base' => $item['uom_base'],
                    'initial_qty' => $item['qty'],
                    'expired_date' => $item['expired_date'],
                    'machine_code' => $item['machine_code'],
                    'status' => 'active',
                ]
            );

            TransactionLog::updateOrCreate(
                ['box_id' => $box->id],
                [
                    'pic_user_id' => 'USR-001',
                    'shift' => 1,
                    'transaction_date' => now(),
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'unit' => $item['uom_input'],
                    'uom_input' => $item['uom_input'],
                    'qty_input' => $item['qty_input'],
                    'conversion_factor' => $item['conversion_factor'],
                    'uom_base' => $item['uom_base'],
                    'qty_base' => $item['qty'],
                    'expired_date' => $item['expired_date'],
                    'machine_code' => $item['machine_code'],
                    'bin_id' => $item['bin_id'],
                    'status' => 'In',
                    'pallet_id' => $item['pallet_id'],
                    'pallet_type_id' => $item['pallet_type_id'],
                    'warehouse_id' => 'GSK.678.I',
                    'notes' => "Initial Seed: {$item['qty_input']} {$item['uom_input']} ({$item['qty']} {$item['uom_base']})",
                ]
            );
        }
    }
}
