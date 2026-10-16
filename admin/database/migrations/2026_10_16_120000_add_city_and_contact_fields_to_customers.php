<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityAndContactFieldsToCustomers extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('customers')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('customers', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable();
            }
            if (!Schema::hasColumn('customers', 'whatsapp_same_as_phone')) {
                $table->boolean('whatsapp_same_as_phone')->default(false);
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('customers')) {
            return;
        }

        $columns = array_filter(['city', 'whatsapp_number', 'whatsapp_same_as_phone'], function ($column) {
            return Schema::hasColumn('customers', $column);
        });
        if ($columns) {
            Schema::table('customers', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
}
