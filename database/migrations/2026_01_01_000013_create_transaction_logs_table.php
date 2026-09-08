<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionLogsTable extends Migration
{
    public function up()
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->string('pic_user_id', 50);
            $table->integer('shift');
            $table->timestamp('transaction_date')->useCurrent();
            $table->string('product_id', 50);
            $table->decimal('qty', 10, 2);
            $table->string('unit', 50);
            $table->string('uom_input', 50)->nullable();
            $table->decimal('qty_input', 10, 2)->nullable();
            $table->decimal('conversion_factor', 10, 2)->default(1.0);
            $table->string('uom_base', 50)->nullable();
            $table->decimal('qty_base', 10, 2)->nullable();
            $table->date('expired_date')->nullable();
            $table->string('machine_code', 50)->nullable();
            $table->string('bin_id', 50);
            $table->enum('status', ['In', 'Out', 'Transfer']);
            $table->string('pallet_id', 50);
            $table->string('pallet_type_id', 50)->nullable();
            $table->string('box_id', 50)->nullable();
            $table->string('warehouse_id', 50);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('pic_user_id')->references('id')->on('users');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('bin_id')->references('id')->on('bins');
            $table->foreign('pallet_id')->references('id')->on('pallets');
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('set null');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaction_logs');
    }
}
