<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTransaksi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');

            $table->string('no_transaksi');
            $table->integer('total_transaksi');
            $table->integer('biaya_pengiriman');
            $table->integer('jarak_tempuh');
            $table->integer('total_biaya_pengiriman');
            $table->string('kode_voucher')->default('-');
            $table->integer('potongan')->default(0);
            $table->integer('total_bayar');
            $table->integer('banyak_item');
            
            $table->enum('alamat_lain',['0','1']);// 0=Tidak, 1=Ya
            $table->text('lat');
            $table->text('long');
            $table->text('detail_alamat');
            
            $table->enum('metode_pembayaran',['1','2','3']);//1=bukpay, 2=cod, 3=bayarditempat
            $table->enum('transaksi_member',['0','1'])->default(0); //0 = bukan member, 1=member
            $table->enum('status',['1','2','3','4','5']); //1= Mempersiapkan Item, 2=pengiriman, 3=item diterima, 4=feedback, 5=selesai
            $table->datetime('tgl_bayar')->nullable();

            $table->text('catatan')->default('-');
            $table->integer('durasi_kirim')->default(0);
            $table->datetime('waktu_kirim');
            
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
}
