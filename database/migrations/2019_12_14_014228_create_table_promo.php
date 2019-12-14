<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePromo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('judul');
            $table->string('gambar');
            $table->date('berlaku_sampai');
            $table->enum('status',['0','1']);
            $table->string('dibuat_oleh');
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
        Schema::dropIfExists('promo');
    }
}
