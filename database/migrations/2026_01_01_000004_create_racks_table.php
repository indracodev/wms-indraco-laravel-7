<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRacksTable extends Migration
{
    public function up()
    {
        Schema::create('racks', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('warehouse_id', 50);
            $table->string('rack_name', 50);
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('racks');
    }
}
