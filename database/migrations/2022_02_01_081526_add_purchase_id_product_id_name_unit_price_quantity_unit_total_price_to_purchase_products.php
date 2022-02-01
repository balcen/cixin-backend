<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseIdProductIdNameUnitPriceQuantityUnitTotalPriceToPurchaseProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_id')->after('id');
            $table->unsignedBigInteger('product_id')->after('purchase_id');
            $table->string('name')->after('product_id');
            $table->decimal('unit_price', 8, 2)->after('name');
            $table->unsignedInteger('quantity')->after('unit_price');
            $table->string('unit')->after('quantity');
            $table->decimal('total_price', 8, 2)->after('unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->dropColumn('purchase_id');
            $table->dropColumn('product_id');
            $table->dropColumn('name');
            $table->dropColumn('unit_price');
            $table->dropColumn('quantity');
            $table->dropColumn('unit');
            $table->dropColumn('total_price');
        });
    }
}
