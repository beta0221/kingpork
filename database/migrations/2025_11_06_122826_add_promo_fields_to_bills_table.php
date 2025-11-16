<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPromoFieldsToBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->string('promo_code', 50)->nullable()->after('kol')->comment('使用的優惠碼');
            $table->integer('promo_discount_amount')->nullable()->after('promo_code')->comment('優惠折扣金額');
            $table->index('promo_code');
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
            $table->dropIndex(['promo_code']);
            $table->dropColumn(['promo_code', 'promo_discount_amount']);
        });
    }
}
