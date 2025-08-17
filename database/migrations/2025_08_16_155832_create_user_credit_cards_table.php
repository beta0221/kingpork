<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCreditCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_credit_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('card_alias')->comment('卡片別名');
            $table->string('masked_card_number')->comment('遮罩卡號');
            $table->string('card_holder_name')->comment('持卡人姓名');
            $table->tinyInteger('expiry_month')->nullable()->comment('到期月份');
            $table->integer('expiry_year')->nullable()->comment('到期年份');
            $table->string('card_brand')->comment('卡片品牌');
            $table->string('ecpay_member_id')->nullable()->comment('ECPay會員編號');
            $table->boolean('is_default')->default(false)->comment('是否為預設卡片');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_credit_cards');
    }
}
