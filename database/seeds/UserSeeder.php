<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id' => 'USR-001',
                'name' => 'Super Admin Indraco',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role_id' => 'super_admin',
                'shift' => 1,
                'is_active' => true,
            ],
            [
                'id' => 'USR-002',
                'name' => 'Budi Santoso',
                'username' => 'budi',
                'password' => Hash::make('budi123'),
                'role_id' => 'supervisor',
                'shift' => 1,
                'is_active' => true,
            ],
            [
                'id' => 'USR-003',
                'name' => 'Gilang Ramadhan',
                'username' => 'gilang',
                'password' => Hash::make('gilang123'),
                'role_id' => 'operator',
                'shift' => 1,
                'is_active' => true,
            ],
            [
                'id' => 'USR-004',
                'name' => 'Eko Wijaya',
                'username' => 'eko',
                'password' => Hash::make('eko123'),
                'role_id' => 'operator',
                'shift' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(['id' => $u['id']], $u);
        }
    }
}
