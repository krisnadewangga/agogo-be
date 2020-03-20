<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableKas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->integer('saldo_awal');
            $table->integer('transaksi');
            $table->integer('saldo_akhir');
            $table->integer('diskon');
            $table->integer('total_refund');
            $table->datetime('tgl_hitung');
            $table->enum('status',['0','1'])->default(0); //0=belum dihitung , 1 = sudah dihitung
            $table->timestamps();

           $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade')->onUpdate('cascade');


        });


     
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_kas');
    }
}
