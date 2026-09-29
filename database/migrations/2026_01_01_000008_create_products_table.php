<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('id', 50)->primary(); // Item No.
            $table->string('name', 255); // Item Description
            $table->string('item_group', 100)->nullable(); // Item Group
            $table->string('group_level_2', 100)->nullable(); // Group Level 2 Name (Coffee, Chocolate, etc.)
            $table->string('group_level_3', 100)->nullable(); // Group Level 3 Name (Ground Coffee, etc.)
            $table->string('brand', 100)->nullable(); // Brand Name
            
            // UOM Hierarchy & Group Config
            $table->string('uom_inventory', 50); // UOM 3 / Base UOM
            $table->string('uom_group', 50)->nullable(); // UoM Group (BOX24, SACHET120, Manual, etc.)
            $table->string('uom_1', 50)->nullable(); // Outer UOM (Karton, KG, etc.)
            $table->string('uom_2', 50)->nullable(); // Intermediate UOM (Renceng, BoxBNS, etc.)
            $table->string('uom_3', 50)->nullable(); // Base UOM (Bag, Sachet, Box, Can, etc.)
            $table->string('karton_to_pcs', 50)->nullable(); // ITEM QTY KARTON TO PCS (e.g. 120 or 'Manual')
            $table->integer('no_of_units')->default(1); // No. of Units
            $table->decimal('qty_per_uom_group', 10, 2)->default(1);
            $table->boolean('is_manual_uom')->default(false);
            
            // Logistical Control Flags & Taxes
            $table->boolean('manage_batch_no')->default(true); // Manage Batch No. [Yes/No]
            $table->boolean('purchase_item')->default(false); // Purchase Item
            $table->boolean('sales_item')->default(true); // Sales Item
            $table->boolean('inventory_item')->default(true); // Inventory Item
            $table->string('sales_tax_definition', 50)->nullable(); // Sales Tax Definition
            $table->string('purchase_tax_definition', 50)->nullable(); // Purchase Tax Definition
            $table->boolean('is_active')->default(true); // Active
            
            // Physical Dimensions & Weight
            $table->decimal('length_cm', 10, 2)->default(0.00); // Length (cm)
            $table->decimal('width_cm', 10, 2)->default(0.00); // Width (cm)
            $table->decimal('height_cm', 10, 2)->default(0.00); // Height (cm)
            $table->decimal('volume_m3', 10, 4)->default(0.0000); // Volume - Sales Unit (m3)
            $table->decimal('weight_gross_kg', 10, 2)->default(0.00); // Weight (kg per Karton)
            $table->decimal('weight_net_g', 10, 2)->default(0.00); // Weight 1 - Inventory (gram)
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
