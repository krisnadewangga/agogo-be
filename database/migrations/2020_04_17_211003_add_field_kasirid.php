<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldKasirid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
         Schema::table('transaksi', function (Blueprint $table) {
        

            $table->unsignedBigInteger('kasir_id')->nullable();
            $table->foreign('kasir_id')->on('users')
                                           ->references('id')
                                           ->onUpdate('cascade')
                                           ->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('kasir_id');
        });
    }
}
