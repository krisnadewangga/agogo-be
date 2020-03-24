<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProduksi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produksi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id');
            $table->integer('produksi1')->nullable();
            $table->integer('produksi2')->nullable();
            $table->integer('produksi3')->nullable();
            $table->integer('total_produksi')->nullable();
            $table->integer('penjualan_toko')->nullable();
            $table->integer('penjualan_pemesanan')->nullable();
            $table->integer('total_penjualan')->nullable();
            $table->integer('ket_rusak')->nullable();
            $table->integer('ket_lain')->nullable();
            $table->integer('total_lain')->nullable();
            $table->string('catatan')->nullable();
            $table->integer('stock_awal');
            $table->integer('sisa_stock');

            $table->timestamps();
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
        Schema::dropIfExists('produksi');
    }
}
