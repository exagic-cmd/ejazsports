<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCargoServiceToCustomersAndSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('customers') && !Schema::hasColumn('customers', 'cargo_service')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('cargo_service')->nullable()->after('address');
            });
        }

        if (Schema::hasTable('suppliers')) {
            Schema::table('suppliers', function (Blueprint $table) {
                if (!Schema::hasColumn('suppliers', 'address')) {
                    $table->string('address')->nullable()->after('email');
                }
                if (!Schema::hasColumn('suppliers', 'cargo_service')) {
                    $table->string('cargo_service')->nullable()->after('address');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'cargo_service')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('cargo_service');
            });
        }

        if (Schema::hasTable('suppliers')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $columnsToDrop = [];
                if (Schema::hasColumn('suppliers', 'cargo_service')) {
                    $columnsToDrop[] = 'cargo_service';
                }
                if (Schema::hasColumn('suppliers', 'address')) {
                    $columnsToDrop[] = 'address';
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
}
