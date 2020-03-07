<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CraeteTableROrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaksi_id');
            $table->integer('discount')->default(0);
            $table->integer('add_fee')->default(0);
            $table->integer('uang_dibayar');
            $table->integer('uang_kembali');
            $table->string('status');
            $table->timestamps();
            $table->foreign('transaksi_id')->on('transaksi')
                                           ->references('id')
                                           ->onUpdate('cascade')
                                           ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('r_order');
    }
}
