<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreClosingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_closings', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('SET NULL');
            $table->double('opening_balance')->default(0);
            $table->double('cash_bills')->default(0);
            $table->double('card_bills')->default(0);
            $table->double('return_bills')->default(0);
            $table->double('expense')->default(0);
            $table->double('expecting_amount')->default(0);
            $table->double('closing_amount')->default(0);
            $table->double('difference')->default(0);
            $table->text('note')->nullable();
            $table->integer('five_coin_count')->default(0);
            $table->integer('Ten_note_count')->default(0);
            $table->integer('twenty_note_count')->default(0);
            $table->integer('fifty_note_count')->default(0);
            $table->integer('hundred_note_count')->default(0);
            $table->integer('five_hundred_note_count')->default(0);
            $table->integer('one_thousand_note_count')->default(0);
            $table->integer('five_thousand_note_count')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('store_closings');
    }
}
