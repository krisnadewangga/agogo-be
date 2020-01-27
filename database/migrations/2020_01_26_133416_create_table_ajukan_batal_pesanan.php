<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAjukanBatalPesanan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ajukan_batal_pesanan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaksi_id');
            $table->string('diajukan_oleh');
            $table->string('disetujui_oleh')->nullable();
            $table->enum('status', ['0','1'])->default('0');//0 Ajukan , 1 Disetujui
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
        Schema::dropIfExists('ajukan_batal_pesanan');
    }
}
