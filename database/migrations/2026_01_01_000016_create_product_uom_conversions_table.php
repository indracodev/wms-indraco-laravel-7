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
            $table->integer('level')->default(1)->comment('1: Outer/Karton, 2: Intermediate/Renceng, 3: Base/Sachet');
            $table->string('uom_convert', 50);
            $table->decimal('qty_convert', 10, 2);
            $table->string('uom_base', 50);
            $table->decimal('multiplier_to_base', 10, 2)->default(1);
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
