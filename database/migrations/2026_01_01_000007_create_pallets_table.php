<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePalletsTable extends Migration
{
    public function up()
    {
        Schema::create('pallets', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('pallet_type_id', 50);
            $table->string('bin_id', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('pallet_type_id')->references('id')->on('pallet_types');
            $table->foreign('bin_id')->references('id')->on('bins')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pallets');
    }
}
