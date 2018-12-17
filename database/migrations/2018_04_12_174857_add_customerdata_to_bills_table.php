<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerdataToBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->string('ship_memo')->nullable()->after('SPToken');
            $table->string('ship_three_company')->nullable()->after('SPToken');
            $table->string('ship_three_id')->nullable()->after('SPToken');
            $table->string('ship_three_name')->nullable()->after('SPToken');
            $table->string('ship_receipt')->nullable()->after('SPToken');
            $table->string('ship_time')->nullable()->after('SPToken');
            $table->string('ship_arriveDate')->nullable()->after('SPToken');
            $table->string('ship_arrive')->nullable()->after('SPToken');
            $table->string('ship_email')->nullable()->after('SPToken');
            $table->mediumtext('ship_address')->nullable()->after('SPToken');
            $table->string('ship_district')->nullable()->after('SPToken');
            $table->string('ship_county')->nullable()->after('SPToken');
            $table->string('ship_phone')->nullable()->after('SPToken');
            $table->integer('ship_gender')->nullable()->after('SPToken');
            $table->string('ship_name')->nullable()->after('SPToken');
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
            $table->dropColumn('ship_memo');
            $table->dropColumn('ship_three_company');
            $table->dropColumn('ship_three_id');
            $table->dropColumn('ship_three_name');
            $table->dropColumn('ship_receipt');
            $table->dropColumn('ship_time');
            $table->dropColumn('ship_arriveDate');
            $table->dropColumn('ship_arrive');
            $table->dropColumn('ship_email');
            $table->dropColumn('ship_address');
            $table->dropColumn('ship_district');
            $table->dropColumn('ship_county');
            $table->dropColumn('ship_phone');
            $table->dropColumn('ship_gender');
            $table->dropColumn('ship_name');
        });
    }
}
