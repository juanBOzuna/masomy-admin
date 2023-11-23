<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->unsignedDouble('subtotal');
            $table->unsignedDouble('disccount');
            $table->unsignedDouble('total');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('orden_id');
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
        Schema::dropIfExists('orden_detail');
    }
}
