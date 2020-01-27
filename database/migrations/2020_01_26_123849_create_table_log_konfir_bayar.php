<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLogKonfirBayar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_konfir_bayar', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaksi_id');
            $table->string('input_by');
            $table->timestamps();
            $table->foreign('transaksi_id')->on('transaksi')->references('id')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_konfir_bayar');
    }
}
