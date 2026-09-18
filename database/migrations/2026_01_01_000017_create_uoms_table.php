<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUomsTable extends Migration
{
    public function up()
    {
        Schema::create('uoms', function (Blueprint $table) {
            $table->string('id', 50)->primary(); // e.g., BOX, PCS, KG, SACHET
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Inisialisasi UOM standar sesuai dengan data eksisting di sistem
        $now = now();
        $defaultUoms = [
            ['id' => 'BOX', 'name' => 'Box', 'description' => 'Satuan kemasan kotak / dus kardus utama'],
            ['id' => 'PCS', 'name' => 'PCS', 'description' => 'Satuan pieces / satuan tunggal eceran'],
            ['id' => 'KG', 'name' => 'KG', 'description' => 'Satuan berat kilogram'],
            ['id' => 'SACHET', 'name' => 'Sachet', 'description' => 'Satuan kemasan sachet kecil'],
            ['id' => 'CAN', 'name' => 'Can', 'description' => 'Satuan kemasan kaleng'],
            ['id' => 'BAG', 'name' => 'Bag', 'description' => 'Satuan kemasan kantong / bag'],
            ['id' => 'KARTON', 'name' => 'Karton', 'description' => 'Satuan karton pengiriman'],
            ['id' => 'PIECES', 'name' => 'Pieces', 'description' => 'Satuan pieces produk'],
        ];

        foreach ($defaultUoms as $uom) {
            DB::table('uoms')->insertOrIgnore([
                'id' => $uom['id'],
                'name' => $uom['name'],
                'description' => $uom['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('uoms');
    }
}
