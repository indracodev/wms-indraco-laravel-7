<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bin;
use App\Models\Pallet;
use App\Models\Box;
use App\Services\WarehouseTransactionService;
use App\Services\ImpersonationService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WMSBusinessRulesTest extends TestCase
{
    /**
     * Uji Aturan Bisnis 1: Kapasitas Maksimal BIN (2 Palet)
     */
    public function test_bin_capacity_limit_max_2_pallets()
    {
        $service = new WarehouseTransactionService();

        // B.10.2 sudah berisi 2 palet dari MasterDataSeeder (PLT-0001 dan PLT-0002)
        $bin = Bin::find('B.10.2');
        $this->assertEquals(2, $bin->pallets()->count());
        $this->assertTrue($bin->isFull());

        // Mencoba menambahkan palet ke-3 (PLT-0099) ke BIN B.10.2 harus melempar Exception
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/sudah penuh/i');

        $service->processInbound([
            'bin_id' => 'B.10.2',
            'pallet_id' => 'PLT-0099',
            'pallet_type_id' => 'loscam',
            'product_id' => 'PRD-99812',
            'qty' => 10,
            'unit' => 'Karton',
            'expired_date' => '2026-12-31',
            'machine_code' => 'MC-TEST',
            'shift' => 1,
        ], 'USR-001');
    }

    /**
     * Uji Aturan Bisnis 2: RBAC Granular Hak Akses User
     */
    public function test_rbac_user_permissions()
    {
        $admin = User::find('USR-001');
        $operator = User::find('USR-003');

        // Super Admin punya akses penuh ke seluruh modul
        $this->assertTrue($admin->hasPermission('rbac', 'read'));
        $this->assertTrue($admin->hasPermission('master', 'create'));

        // Operator Gudang hanya punya akses ke Inbound/Outbound/Transfer, tidak punya akses ke RBAC/Users/Settings
        $this->assertTrue($operator->hasPermission('inbound', 'create'));
        $this->assertFalse($operator->hasPermission('rbac', 'read'));
        $this->assertFalse($operator->hasPermission('users', 'create'));
    }

    /**
     * Uji Aturan Bisnis 3: Engine Impersonasi Admin Login-As & Revert
     */
    public function test_admin_impersonation_flow()
    {
        $admin = User::find('USR-001');
        $operator = User::find('USR-003');

        $this->actingAs($admin);
        $this->assertEquals('USR-001', auth()->id());

        $impersonationService = new ImpersonationService();
        $impersonationService->impersonate($operator);

        // Setelah impersonate, auth user berubah ke Operator & session menyimpan impersonator_id
        $this->assertEquals('USR-003', auth()->id());
        $this->assertTrue(session()->has('impersonator_id'));
        $this->assertEquals('USR-001', session()->get('impersonator_id'));

        // Revert kembali ke Admin
        $impersonationService->revert();
        $this->assertEquals('USR-001', auth()->id());
        $this->assertFalse(session()->has('impersonator_id'));
    }

    /**
     * Uji Aturan Bisnis 4: Siklus Mutasi Lengkap (Inbound -> Transfer -> Outbound)
     */
    public function test_full_inventory_mutation_cycle()
    {
        $service = new WarehouseTransactionService();

        // 1. Inbound 15 Karton ke BIN A.1.1 (Palet PLT-999)
        $box = $service->processInbound([
            'bin_id' => 'A.1.1',
            'pallet_id' => 'PLT-999',
            'pallet_type_id' => 'loscam',
            'product_id' => 'PRD-99812',
            'qty' => 15,
            'unit' => 'Karton',
            'expired_date' => '2026-12-31',
            'machine_code' => 'MC-TEST',
            'shift' => 1,
        ], 'USR-001');

        $this->assertNotNull($box);
        $pallet = Pallet::find('PLT-999');
        $this->assertEquals('A.1.1', $pallet->bin_id);

        // 2. Transfer PLT-999 dari A.1.1 ke A.1.2
        $movedPallet = $service->processTransfer('PLT-999', 'A.1.2', 'USR-001');
        $this->assertEquals('A.1.2', $movedPallet->bin_id);

        // 3. Outbound 15 Karton dari PLT-999
        $service->processOutbound([
            'pallet_id' => 'PLT-999',
            'product_id' => 'PRD-99812',
            'qty' => 15,
            'shift' => 1,
        ], 'USR-001');

        // Palet PLT-999 setelah stok 0 menjadi non-aktif
        $palletAfterOutbound = Pallet::find('PLT-999');
        $this->assertFalse((bool)$palletAfterOutbound->is_active);
        $this->assertNull($palletAfterOutbound->bin_id);
    }

    /**
     * Uji Aturan Bisnis 5: Alur Inbound Terpisah (Penempatan Palet ke BIN & Pendataan Box ke Palet)
     */
    public function test_inbound_pallet_to_bin_and_box_to_pallet_flow()
    {
        $service = new WarehouseTransactionService();

        // 1. Assign Palet PLT-8888 ke BIN A.1.1
        $pallet = $service->assignPalletToBin('A.1.1', 'PLT-8888', 'loscam', 'USR-001');
        $this->assertNotNull($pallet);
        $this->assertEquals('A.1.1', $pallet->bin_id);
        $this->assertTrue((bool)$pallet->is_active);

        // 2. Attach Box ke Palet PLT-8888
        $box = $service->attachBoxToPallet([
            'pallet_id' => 'PLT-8888',
            'box_id' => 'BOX-8888',
            'product_id' => 'PRD-99812',
            'qty' => 25,
            'unit' => 'Karton',
            'expired_date' => '2027-01-01',
            'machine_code' => 'MC-02',
            'shift' => 1,
        ], 'USR-001');

        $this->assertNotNull($box);
        $this->assertEquals('PLT-8888', $box->pallet_id);
        $this->assertEquals(25, $box->qty);
    }
}
