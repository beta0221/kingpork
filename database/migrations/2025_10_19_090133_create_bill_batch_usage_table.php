<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillBatchUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_batch_usage', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bill_id');
            $table->integer('inventory_batch_id')->unsigned();
            $table->integer('quantity_used');
            $table->integer('shipment_plan_id')->unsigned()->nullable();
            $table->timestamps();

            // 外鍵約束
            $table->foreign('inventory_batch_id')->references('id')->on('inventory_batches')->onDelete('cascade');
            $table->foreign('shipment_plan_id')->references('id')->on('shipment_plans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_batch_usage');
    }
}
