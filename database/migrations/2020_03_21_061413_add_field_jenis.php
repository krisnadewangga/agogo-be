<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldJenis extends Migration
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
        
             $table->enum('jenis',['1','2'])->default(1)->after('jalur');
            //  1 = shoping langsung , 2 = pesananan
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
            $table->dropColumn('jenis');
        });
    }
}
