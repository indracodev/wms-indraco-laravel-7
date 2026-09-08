<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBinsTable extends Migration
{
    public function up()
    {
        Schema::create('bins', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('rack_id', 50);
            $table->integer('column_no');
            $table->integer('level_no');
            $table->integer('max_pallet')->default(2);
            $table->timestamps();

            $table->foreign('rack_id')->references('id')->on('racks')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bins');
    }
}
