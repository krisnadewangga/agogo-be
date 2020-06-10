<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableNotifikasi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pengirim_id');
            $table->integer('penerima_id');
            $table->integer('judul_id');
            $table->text('judul');
            $table->text('isi');
            $table->enum('jenis_notif',['1','2','3','4','5','6','7','8','9']);
            //1 = transaksi
            //2 = pengiriman
            //3 = terima pengiriman
            //4 = topup
            //5 = pesan
            //6 = Batal pesanan
            //7 = konfir bayar
            //8 = ambil pesanan
            //9 = pembayaran pesanan

            $table->enum('dibaca',['0','1'])->default(0);
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
        Schema::dropIfExists('notifikasi');
    }
}
