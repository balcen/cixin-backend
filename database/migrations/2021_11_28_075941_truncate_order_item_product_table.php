<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TruncateOrderItemProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('order_item_product');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('order_item_product', function (Blueprint $table) {
            $table->unsignedInteger('order_item_id');
            $table->unsignedInteger('product_id');
            $table->string('unit');
            $table->decimal('price', 8,2);
            $table->decimal('total_price', 8, 2);
            $table->integer('quantity');
        });
    }
}
