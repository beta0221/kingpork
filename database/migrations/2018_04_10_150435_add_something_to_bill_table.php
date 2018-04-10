<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomethingToBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->integer('RtnCode')->nullable();
            $table->string('RtnMsg')->nullable();
            $table->string('TradeNo')->nullable();
            $table->string('PaymentDate')->nullable();
            $table->integer('PaymentTypeChargeFee')->nullable();
            $table->string('TradeDate')->nullable();
            $table->integer('SimulatePaid')->nullable();
            $table->string('allReturn')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('all');
            $table->dropColumn('SimulatePaid');
            $table->dropColumn('TradeDate');
            $table->dropColumn('PaymentTypeChargeFee');
            $table->dropColumn('PaymentDate');
            $table->dropColumn('TradeNo');
            $table->dropColumn('RtnMsg');
            $table->dropColumn('RtnCode');
        });
    }
}
