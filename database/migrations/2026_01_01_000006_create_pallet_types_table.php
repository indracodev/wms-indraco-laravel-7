<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePalletTypesTable extends Migration
{
    public function up()
    {
        Schema::create('pallet_types', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('name', 50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pallet_types');
    }
}
