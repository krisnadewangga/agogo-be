<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePengiriman extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaksi_id');
            $table->unsignedBigInteger('kurir_id');
            $table->datetime('dikirimkan');
            $table->datetime('diterima')->nullable();
            $table->string('diterima_oleh')->nullable();
            $table->enum('status',['0','1']); //0 = sementara pengiriman, 1= diterima 
            $table->timestamps();
            
            $table->foreign('transaksi_id')->on('transaksi')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kurir_id')->references('id')->on('kurir')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengiriman');
    }
}
