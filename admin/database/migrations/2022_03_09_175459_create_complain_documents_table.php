<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplainDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complain_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complain_id')->nullable();
            $table->foreign('complain_id')->references('id')->on('complains')->onDelete('SET NULL');
            $table->string('url');
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
        Schema::dropIfExists('complain_documents');
    }
}
