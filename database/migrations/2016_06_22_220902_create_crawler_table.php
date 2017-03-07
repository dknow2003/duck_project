<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrawlerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawler_data', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->comment('数据时间');
            $table->integer('value')->default(0)->comment('数值');
            $table->tinyInteger('type')->default(0)->comment('数据类型');
            $table->integer('server_id')->default(0)->comment('server');
            $table->text('data')->default('')->comment('额外数据，存每小时 pcu');
            $table->index(['date', 'type', 'server_id']);
        });
        Schema::create('crawler_status', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->comment('统计时间');
            $table->tinyInteger('status')->comment('统计状态');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('crawler_status');
        Schema::drop('crawler_data');
    }
}
