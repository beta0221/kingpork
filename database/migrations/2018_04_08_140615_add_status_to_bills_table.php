<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->string('status')->after('price')->default('0');
            $table->string('pay_by')->after('status')->default('0');
            $table->string('SPToken')->nullable()->after('pay_by');
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
            $table->dropColumn('SPToken');
            $table->dropColumn('pay_by');
            $table->dropColumn('status');
        });
    }
}
