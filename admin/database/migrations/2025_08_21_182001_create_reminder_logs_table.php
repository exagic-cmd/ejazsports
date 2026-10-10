<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReminderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('reminder_logs')) {
            Schema::create('reminder_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('reminder_id');
                $table->date('log_date');
                $table->string('time_slot');
                $table->timestamp('dismissed_at')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('reminder_logs');
    }
}
