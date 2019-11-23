<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableItemTransaksi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_transaksi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaksi_id');
            $table->unsignedBigInteger('item_id');
            // $table->string('v_rasa');
            $table->integer('jumlah');
            $table->integer('harga');
            $table->integer('margin');
            $table->integer('diskon')->default(0);
            $table->integer('harga_diskon')->default(0);
            $table->integer('total');
            $table->timestamps();
            $table->foreign('transaksi_id')->on('transaksi')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('item_id')->on('item')->references('id')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_transaksi');
    }
}
