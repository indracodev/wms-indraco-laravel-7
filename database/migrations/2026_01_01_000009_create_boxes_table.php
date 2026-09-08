<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxesTable extends Migration
{
    public function up()
    {
        Schema::create('boxes', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('pallet_id', 50);
            $table->string('product_id', 50);
            $table->decimal('qty', 10, 2);
            $table->string('uom_base', 50)->default('Box');
            $table->decimal('initial_qty', 10, 2)->default(0);
            $table->date('expired_date');
            $table->string('machine_code', 50);
            $table->enum('status', ['active', 'empty'])->default('active');
            $table->timestamps();

            $table->foreign('pallet_id')->references('id')->on('pallets')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    public function down()
    {
        Schema::dropIfExists('boxes');
    }
}
