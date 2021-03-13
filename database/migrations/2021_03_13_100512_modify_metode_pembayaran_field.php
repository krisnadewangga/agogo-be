<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class ModifyMetodePembayaranField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            DB::statement("ALTER TABLE transaksi MODIFY COLUMN metode_pembayaran ENUM('1', '2', '3','4')");
        });
        //4 COD - metode_pembayaran
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaksi', function (Blueprint $table) {
             DB::statement("ALTER TABLE transaksi MODIFY COLUMN metode_pembayaran ENUM('1', '2', '3')");
        });
    }
}
