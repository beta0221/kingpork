<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionalLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotional_links', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50)->unique()->comment('優惠碼（唯一）');
            $table->string('name', 100)->comment('活動名稱');
            $table->decimal('discount_percentage', 5, 2)->comment('折扣百分比（如 10.00 = 9折）');
            $table->json('applicable_categories')->nullable()->comment('適用的商品類別 ID（JSON 陣列）');
            $table->dateTime('start_date')->comment('開始日期');
            $table->dateTime('end_date')->comment('結束日期');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->integer('usage_count')->default(0)->comment('使用次數統計');
            $table->timestamps();

            $table->index('code');
            $table->index(['is_active', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotional_links');
    }
}
