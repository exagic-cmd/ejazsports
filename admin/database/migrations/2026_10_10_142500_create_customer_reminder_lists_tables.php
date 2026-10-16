<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerReminderListsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('customer_reminder_lists')) {
            Schema::create('customer_reminder_lists', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->date('reminder_date')->index();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->tinyInteger('status')->default(1)->index()->comment('1=Active, 2=Completed');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('customer_reminder_list_items')) {
            Schema::create('customer_reminder_list_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('reminder_list_id')->index();
                $table->unsignedBigInteger('customer_id')->index();
                $table->decimal('snapshot_balance', 15, 2)->default(0.00);
                $table->tinyInteger('status')->default(1)->comment('1=Pending, 2=Called, 3=Paid, 4=Rescheduled, 5=Cancelled');
                $table->string('remarks', 500)->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('reminder_list_id')
                      ->references('id')
                      ->on('customer_reminder_lists')
                      ->onDelete('cascade');
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
        Schema::dropIfExists('customer_reminder_list_items');
        Schema::dropIfExists('customer_reminder_lists');
    }
}
