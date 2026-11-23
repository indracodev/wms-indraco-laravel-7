<?php

use Illuminate\Database\Seeder;

require_once __DIR__ . '/BinLocationSeeder.php';
require_once __DIR__ . '/FgdPrmMasterSeeder.php';
require_once __DIR__ . '/ProductMasterSeeder.php';
require_once __DIR__ . '/PalletListSeeder.php';
require_once __DIR__ . '/MasterDataSeeder.php';
require_once __DIR__ . '/RoleSeeder.php';
require_once __DIR__ . '/UserSeeder.php';

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(MasterDataSeeder::class);
    }
}
