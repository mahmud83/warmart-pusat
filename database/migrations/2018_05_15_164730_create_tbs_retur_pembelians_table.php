<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbsReturPembeliansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbs_retur_pembelians', function (Blueprint $table) {
            $table->increments('id_tbs_retur_pembelian');
            $table->string('session_id');
            $table->string('no_faktur_pembelian')->nullable();
            $table->integer('id_produk');
            $table->float('jumlah_beli', 100,2)->default(0.00); 
            $table->float('jumlah_retur', 100,2)->default(0.00); 
            $table->integer('satuan_id'); 
            $table->integer('satuan_dasar'); 
            $table->integer('satuan_beli'); 
            $table->float('harga_produk', 100,2)->default(0.00); 
            $table->float('subtotal', 100,2)->default(0.00); 
            $table->float('potongan', 100,6)->default(0.00)->nullable();  
            $table->float('tax', 100,6)->default(0.00)->nullable(); 
            $table->integer('warung_id');
            $table->unsignedInteger('created_by')->nullable()->index();            
            $table->unsignedInteger('updated_by')->nullable()->index();
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
        Schema::dropIfExists('tbs_retur_pembelians');
    }
}
