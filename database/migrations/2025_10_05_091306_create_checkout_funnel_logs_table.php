<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckoutFunnelLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkout_funnel_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('session_id')->index();
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->string('bill_id')->nullable()->index();
            $table->string('step')->index(); // 流程步驟名稱
            $table->string('status')->default('success'); // success, error, abandoned
            $table->text('error_message')->nullable();
            $table->text('metadata')->nullable(); // JSON 格式額外資料
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('payment_method')->nullable(); // CREDIT, ATM, cod, FAMILY
            $table->integer('amount')->unsigned()->nullable();
            $table->timestamps();

            // 複合索引用於查詢分析
            $table->index(['session_id', 'step']);
            $table->index(['created_at', 'step']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checkout_funnel_logs');
    }
}
