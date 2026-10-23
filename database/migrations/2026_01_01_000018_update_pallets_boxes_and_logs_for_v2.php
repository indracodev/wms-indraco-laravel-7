<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdatePalletsBoxesAndLogsForV2 extends Migration
{
    public function up()
    {
        // 1. Update pallets table
        Schema::table('pallets', function (Blueprint $table) {
            if (!Schema::hasColumn('pallets', 'status')) {
                $table->enum('status', ['in_transit', 'on_rack', 'empty', 'dispatched'])->default('in_transit')->after('bin_id');
            }
            if (!Schema::hasColumn('pallets', 'max_capacity')) {
                $table->integer('max_capacity')->default(72)->after('status');
            }
        });

        // Populate initial status for existing pallets based on bin_id
        DB::table('pallets')->whereNotNull('bin_id')->where('bin_id', '!=', '')->update(['status' => 'on_rack']);
        DB::table('pallets')->whereNull('bin_id')->orWhere('bin_id', '')->update(['status' => 'in_transit']);

        // 2. Update boxes table
        Schema::table('boxes', function (Blueprint $table) {
            if (!Schema::hasColumn('boxes', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });

        // 3. Update transaction_logs table via direct SQL to avoid doctrine/dbal requirement
        try {
            DB::statement("ALTER TABLE transaction_logs DROP FOREIGN KEY transaction_logs_bin_id_foreign");
        } catch (\Exception $e) {
            // Already dropped or did not exist
        }

        try {
            DB::statement("ALTER TABLE transaction_logs MODIFY bin_id VARCHAR(50) NULL");
        } catch (\Exception $e) {
            // Proceed
        }

        try {
            DB::statement("ALTER TABLE transaction_logs MODIFY status VARCHAR(50) NOT NULL DEFAULT 'In'");
        } catch (\Exception $e) {
            // Proceed
        }
    }

    public function down()
    {
        Schema::table('pallets', function (Blueprint $table) {
            if (Schema::hasColumn('pallets', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('pallets', 'max_capacity')) {
                $table->dropColumn('max_capacity');
            }
        });

        Schema::table('boxes', function (Blueprint $table) {
            if (Schema::hasColumn('boxes', 'notes')) {
                $table->dropColumn('notes');
            }
        });

        try {
            DB::statement("ALTER TABLE transaction_logs MODIFY status ENUM('In', 'Out', 'Transfer') NOT NULL");
        } catch (\Exception $e) {
            // Proceed
        }
    }
}
