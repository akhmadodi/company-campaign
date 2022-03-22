<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_images', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->on('customers')->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('campaign_id');
            $table->foreign('campaign_id')->on('campaigns')->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_images');
    }
}
