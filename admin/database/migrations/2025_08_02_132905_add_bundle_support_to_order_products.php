<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBundleSupportToOrderProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->unsignedBigInteger('bundle_id')->nullable()->after('variant_id');
            $table->unsignedBigInteger('parent_id')->nullable()->after('bundle_id');
            $table->boolean('is_bundle')->default(false)->after('parent_id');
            $table->boolean('is_bundle_item')->default(false)->after('is_bundle');

            $table->foreign('bundle_id')->references('id')->on('bundles')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('order_products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('order_products', function (Blueprint $table) {
            $table->dropForeign(['bundle_id']);
            $table->dropForeign(['parent_id']);

            $table->dropColumn(['bundle_id', 'parent_id', 'is_bundle', 'is_bundle_item']);
        });
    }
}
