<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('plan_name');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->json('plan_data');
            $table->integer('total_orders')->default(0);
            $table->integer('total_stages')->default(0);
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
        Schema::dropIfExists('shipment_plans');
    }
}
