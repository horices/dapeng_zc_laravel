<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRobotAllowLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_headmaster', function (Blueprint $table) {
            $table->tinyInteger("robot_allow_login")->default(0)->nullable(false)->comment("是否允许登陆cleverQQ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_headmaster', function (Blueprint $table) {
            $table->dropColumn("robot_allow_login");
        });
    }
}
