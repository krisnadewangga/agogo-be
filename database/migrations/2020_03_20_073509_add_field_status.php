<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('item_transaksi', function (Blueprint $table) {
        
             $table->enum('status',['0','1'])->default(1)->after('total');
            // 1 = aktif, 0 = refund
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
         Schema::table('item_transaksi', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
