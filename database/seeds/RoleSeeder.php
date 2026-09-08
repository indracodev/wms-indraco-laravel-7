<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\RolePermission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'id' => 'super_admin',
                'name' => 'Super Admin',
                'description' => 'Akses penuh seluruh modul dan kontrol impersonasi user'
            ],
            [
                'id' => 'supervisor',
                'name' => 'Supervisor Gudang',
                'description' => 'Otorisasi transaksi, manajemen master data, dan laporan stok'
            ],
            [
                'id' => 'operator',
                'name' => 'Operator Gudang',
                'description' => 'Eksekusi transaksi Inbound, Outbound, dan Transfer BIN'
            ],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(['id' => $r['id']], $r);
        }

        $modules = ['inbound', 'outbound', 'transfer', 'master', 'pallet', 'rbac', 'users', 'settings', 'logs', 'detail', 'locator'];

        // Role Permission Matrix Setup
        foreach ($modules as $mod) {
            // Super Admin
            RolePermission::updateOrCreate(
                ['role_id' => 'super_admin', 'module' => $mod],
                ['can_read' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true]
            );

            // Supervisor
            $supCanCreate = in_array($mod, ['inbound', 'outbound', 'transfer', 'master', 'detail', 'locator']);
            RolePermission::updateOrCreate(
                ['role_id' => 'supervisor', 'module' => $mod],
                ['can_read' => true, 'can_create' => $supCanCreate, 'can_update' => $supCanCreate, 'can_delete' => false]
            );

            // Operator
            $opCanRead = in_array($mod, ['inbound', 'outbound', 'transfer', 'detail', 'locator']);
            $opCanCreate = in_array($mod, ['inbound', 'outbound', 'transfer']);
            RolePermission::updateOrCreate(
                ['role_id' => 'operator', 'module' => $mod],
                ['can_read' => $opCanRead, 'can_create' => $opCanCreate, 'can_update' => false, 'can_delete' => false]
            );
        }
    }
}
