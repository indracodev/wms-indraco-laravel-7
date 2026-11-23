<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBeamLengthAndBarcodeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('bins', 'beam_length')) {
            Schema::table('bins', function (Blueprint $table) {
                $table->integer('beam_length')->nullable()->default(230)->after('max_pallet');
            });
        }

        if (!Schema::hasColumn('products', 'barcode')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('barcode', 100)->nullable()->after('brand');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('bins', 'beam_length')) {
            Schema::table('bins', function (Blueprint $table) {
                $table->dropColumn('beam_length');
            });
        }

        if (Schema::hasColumn('products', 'barcode')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('barcode');
            });
        }
    }
}
