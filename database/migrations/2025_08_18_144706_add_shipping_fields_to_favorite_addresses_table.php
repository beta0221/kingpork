<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShippingFieldsToFavoriteAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('favorite_addresses', function (Blueprint $table) {
            $table->string('ship_name')->nullable()->comment('收件人姓名');
            $table->string('ship_phone')->nullable()->comment('收件人電話');
            $table->string('ship_email')->nullable()->comment('收件人信箱');
            $table->unsignedTinyInteger('ship_receipt')->nullable()->comment('發票類型: 2=二聯, 3=三聯');
            $table->string('ship_three_id')->nullable()->comment('統一編號');
            $table->string('ship_three_company')->nullable()->comment('公司名稱');
            $table->unsignedTinyInteger('ship_gender')->nullable()->comment('性別: 1=先生, 2=小姐');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('favorite_addresses', function (Blueprint $table) {
            $table->dropColumn([
                'ship_name',
                'ship_phone', 
                'ship_email',
                'ship_receipt',
                'ship_three_id',
                'ship_three_company',
                'ship_gender'
            ]);
        });
    }
}
