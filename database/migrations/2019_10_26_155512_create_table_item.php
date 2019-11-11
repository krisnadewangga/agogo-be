<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kategori_id');
            $table->string('nama_item');
            $table->integer('harga');
            $table->integer('margin');
            $table->integer('stock');
            $table->string('v_rasa')->nullable();
            $table->text('deskripsi');
            $table->string('diinput_by');
            $table->enum('status_aktif',['0','1'])->default('1');
            $table->timestamps();
            $table->foreign('kategori_id')->on('kategori')->references('id')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item');
    }
}
