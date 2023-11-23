<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreOrdenDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_orden_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->unsignedDouble('subtotal');
            $table->unsignedDouble('disccount');
            $table->unsignedDouble('total');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('pre_orden_id');
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
        Schema::dropIfExists('pre_orden_detail');
    }
}
