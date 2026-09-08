<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductUomConversionsTable extends Migration
{
    public function up()
    {
        Schema::create('product_uom_conversions', function (Blueprint $table) {
            $table->id();
            $table->string('product_id', 50);
            $table->string('uom_convert', 50);
            $table->decimal('qty_convert', 10, 2);
            $table->string('uom_base', 50);
            $table->timestamps();

            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_uom_conversions');
    }
}
