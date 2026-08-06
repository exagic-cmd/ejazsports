<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSupplierBransDiscountTerms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers_brands', function (Blueprint $table) {
            $table->float('additional_discount')->nullable();
            $table->float('marketing_discount')->nullable();
            $table->integer('payment_terms')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers_brands', function (Blueprint $table) {
            //
        });
    }
}
