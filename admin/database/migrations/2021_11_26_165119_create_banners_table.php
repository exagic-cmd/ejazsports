<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->text('web_image');
            $table->text('mobile_image');
            $table->integer('serial_no')->default(0);
            $table->string('web_heading')->nullable();
            $table->string('web_sub_heading')->nullable();
            $table->string('mobile_heading')->nullable();
            $table->string('mobile_sub_heading')->nullable();
            $table->string('url')->default('#');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('banners');
    }
}
