<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('活動名稱');
            $table->decimal('multiplier', 5, 2)->default(1.00)->comment('紅利倍數');
            $table->dateTime('start_time')->comment('開始時間');
            $table->dateTime('end_time')->comment('結束時間');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
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
        Schema::dropIfExists('bonus_promotions');
    }
}
