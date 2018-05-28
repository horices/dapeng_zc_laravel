<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SaveHeadmasterCourseStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //添加保存课程顾问开课后的状态
        Schema::table("user_headmaster",function (Blueprint $table){
            $table->tinyInteger("is_open_course")->default(0)->nullable(false)->comment("该课程顾问是否已经开课标识");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("user_headmaster",function (Blueprint $table){
            $table->dropColumn(["is_open_course"]);
        });
    }
}
