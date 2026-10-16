<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerReminderListAuditsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('customer_reminder_list_audits')) {
            Schema::create('customer_reminder_list_audits', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('reminder_list_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('action', 100);
                $table->string('subject_type', 100)->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->text('description');
                $table->text('old_values')->nullable();
                $table->text('new_values')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('customer_reminder_list_audits');
    }
}
