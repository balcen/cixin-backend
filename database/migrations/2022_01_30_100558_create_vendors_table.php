<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->string('name');
            $table->string('abbreviation');
            $table->string('principal')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('invoice_address')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_tel_1')->nullable();
            $table->string('company_tel_2')->nullable();
            $table->string('company_tel_3')->nullable();
            $table->string('company_fax')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_url')->nullable();
            $table->string('online_order_number')->nullable();
            $table->string('online_order_password')->nullable();
            $table->integer('payment')->default(1);
            $table->text('note')->nullable();
            $table->boolean('display')->default(1);
            $table->integer('type')->default(1);
            $table->softDeletes();
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
        Schema::dropIfExists('vendors');
    }
}
