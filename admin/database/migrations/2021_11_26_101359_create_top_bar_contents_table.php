<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopBarContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_bar_contents', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->boolean('mobile_active')->default(1);
            $table->boolean('web_active')->default(1);
            $table->boolean('status')->default(1);
            $table->integer('serial_no')->default(1);
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
        Schema::dropIfExists('top_bar_contents');
    }
}
