<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_item_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_item_id');
            $table->string('name');
            $table->decimal('unit_price', 8, 2);
            $table->integer('quantity');
            $table->string('unit');
            $table->decimal('total_price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_item_products');
    }
}
