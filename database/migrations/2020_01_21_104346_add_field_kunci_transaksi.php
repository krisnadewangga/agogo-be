<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldKunciTransaksi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detail_konsumen', function (Blueprint $table) {
            $table->enum('kunci_transaksi',['0','1'])->default('0'); // 0 buka, 1 // kunci
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_konsumen', function (Blueprint $table) {
            $table->dropColumn('kunci_transaksi');
        });
    }
}
