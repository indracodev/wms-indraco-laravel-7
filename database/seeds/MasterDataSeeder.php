<?php

use Illuminate\Database\Seeder;
use App\Models\Office;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\Rack;
use App\Models\Bin;
use App\Models\PalletType;
use App\Models\Product;
use App\Models\Pallet;
use App\Models\Box;
use App\Models\TransactionLog;

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

        // 3. Warehouse
        $warehouse = Warehouse::updateOrCreate(
            ['id' => 'GSK.678.I'],
            ['branch_id' => $branch->id, 'name' => 'Gudang Bahan Baku & Jadi Gresik', 'address' => 'Kawasan Industri Gresik Blok A-12']
        );

        // 4. Racks
        $racks = [
            ['id' => 'RK-A', 'warehouse_id' => $warehouse->id, 'rack_name' => 'A'],
            ['id' => 'RK-B', 'warehouse_id' => $warehouse->id, 'rack_name' => 'B'],
            ['id' => 'RK-C', 'warehouse_id' => $warehouse->id, 'rack_name' => 'C'],
            ['id' => 'RK-L', 'warehouse_id' => $warehouse->id, 'rack_name' => 'L'],
        ];
        foreach ($racks as $r) {
            Rack::updateOrCreate(['id' => $r['id']], $r);
        }

        // 5. Bins
        $bins = [
            ['id' => 'B.10.1', 'rack_id' => 'RK-B', 'column_no' => 10, 'level_no' => 1, 'max_pallet' => 2],
            ['id' => 'B.10.2', 'rack_id' => 'RK-B', 'column_no' => 10, 'level_no' => 2, 'max_pallet' => 2],
            ['id' => 'C.1.1',  'rack_id' => 'RK-C', 'column_no' => 1,  'level_no' => 1, 'max_pallet' => 2],
            ['id' => 'L.5.1',  'rack_id' => 'RK-L', 'column_no' => 5,  'level_no' => 1, 'max_pallet' => 2],
            ['id' => 'A.1.1',  'rack_id' => 'RK-A', 'column_no' => 1,  'level_no' => 1, 'max_pallet' => 2],
            ['id' => 'A.1.2',  'rack_id' => 'RK-A', 'column_no' => 2,  'level_no' => 2, 'max_pallet' => 2],
        ];
        foreach ($bins as $b) {
            Bin::updateOrCreate(['id' => $b['id']], $b);
        }

        // 6. Pallet Types
        $palletTypes = [
            ['id' => 'loscam', 'name' => 'Palet Loscam Wood'],
            ['id' => 'kayu_125', 'name' => 'Palet Kayu Standar (125cm)'],
            ['id' => 'plastik', 'name' => 'Palet Plastik Heavy Duty'],
        ];
        foreach ($palletTypes as $pt) {
            PalletType::updateOrCreate(['id' => $pt['id']], $pt);
        }

        // 7. Products (Run ProductMasterSeeder from Excel)
        $this->call(ProductMasterSeeder::class);

        // Fallback demo products if not present
        $demoProducts = [
            ['id' => 'PRD-99812', 'name' => 'Kopi Kapal Api Special 165g', 'uom_inventory' => 'Karton', 'item_group' => 'FINISHED GOODS (MANUFAKTUR)', 'brand' => 'Kapal Api'],
            ['id' => 'PRD-99813', 'name' => 'Kopi ABC Susu 31g', 'uom_inventory' => 'Karton', 'item_group' => 'FINISHED GOODS (MANUFAKTUR)', 'brand' => 'ABC'],
            ['id' => 'PRD-99814', 'name' => 'Kopi Ya! Susu 25g', 'uom_inventory' => 'Karton', 'item_group' => 'FINISHED GOODS (MANUFAKTUR)', 'brand' => 'Kopi Ya'],
        ];
        foreach ($demoProducts as $p) {
            Product::updateOrCreate(['id' => $p['id']], $p);
        }

        // 8. Pallets, Boxes & Transaction Logs Initial Seed
        $initialData = [
            [
                'pallet_id' => 'PLT-0001',
                'pallet_type_id' => 'loscam',
                'bin_id' => 'B.10.2',
                'box_id' => 'BOX-0001',
                'product_id' => 'FGD00519', // FGD00519 has 144 Sachet per Karton
                'qty' => 144, // 1 Karton = 144 Sachet
                'uom_base' => 'Sachet',
                'uom_input' => 'KARTON',
                'qty_input' => 1,
                'conversion_factor' => 144,
                'expired_date' => '2026-12-31',
                'machine_code' => 'MC-01',
            ],
            [
                'pallet_id' => 'PLT-0002',
                'pallet_type_id' => 'kayu_125',
                'bin_id' => 'B.10.2',
                'box_id' => 'BOX-0002',
                'product_id' => 'PRD-99812',
                'qty' => 20,
                'uom_base' => 'Karton',
                'uom_input' => 'Karton',
                'qty_input' => 20,
                'conversion_factor' => 1,
                'expired_date' => '2026-10-15',
                'machine_code' => 'MC-02',
            ],
            [
                'pallet_id' => 'PLT-0003',
                'pallet_type_id' => 'plastik',
                'bin_id' => 'C.1.1',
                'box_id' => 'BOX-0003',
                'product_id' => 'PRD-99814',
                'qty' => 30,
                'uom_base' => 'Karton',
                'uom_input' => 'Karton',
                'qty_input' => 30,
                'conversion_factor' => 1,
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

            TransactionLog::create([
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
                'box_id' => $box->id,
                'warehouse_id' => 'GSK.678.I',
                'notes' => "Initial Seed: {$item['qty_input']} {$item['uom_input']} ({$item['qty']} {$item['uom_base']})",
            ]);
        }
    }
}
