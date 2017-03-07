<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('is_super')->default(0)->comment('是否超级管理员');
            $table->string('channel')->default('')->comment('渠道限制');
            $table->string('full_name')->default('');
            $table->text('available_servers')->comment('备选服务器');
            $table->string('selected_server')->comment('当前服务器');
            $table->rememberToken();
            $table->tinyInteger('status')->default(1)->commit('是否禁用');
            $table->softDeletes();
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
        Schema::drop('users');
    }
}
