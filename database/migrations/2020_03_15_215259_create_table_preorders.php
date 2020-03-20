<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePreorders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preorders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaksi_id');
            $table->string('nama');
            $table->date('tgl_pesan')->timestamps();
            $table->date('tgl_selesai');
            $table->string('waktu_selesai');
            $table->string('telepon');
            $table->integer('subtotal');
            $table->integer('discount')->nullable();            
            $table->integer('add_fee')->nullable();
            $table->integer('total');
            $table->integer('sisa_harus_bayar');
            $table->integer('uang_muka')->nullable();
            $table->integer('uang_dibayar')->nullable();
            $table->integer('uang_kembali')->nullable();
            $table->timestamps();

             $table->foreign('transaksi_id')->references('id')->on("transaksi")->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preorders');
    }
}
