<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraCommissionToProductsAndOrderProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('extra_com_status')->default(false)->after('dz_price');
            $table->tinyInteger('extra_com_type')->default(1)->after('extra_com_status'); // 1 = fixed Rs per piece, 2 = % of margin
            $table->decimal('extra_com_retail', 10, 2)->default(0)->after('extra_com_type');
            $table->decimal('extra_com_whole', 10, 2)->default(0)->after('extra_com_retail');
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->decimal('extra_commission', 10, 2)->default(0)->after('wholesale_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['extra_com_status', 'extra_com_type', 'extra_com_retail', 'extra_com_whole']);
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('extra_commission');
        });
    }
}
