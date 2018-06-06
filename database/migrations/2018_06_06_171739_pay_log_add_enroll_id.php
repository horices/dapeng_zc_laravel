<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PayLogAddEnrollId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //允许 user_enroll 直接关联支付日志,查询最后一条支付信息
        Schema::table("user_pay_log",function (Blueprint $table){
            $table->integer("enroll_id")->default(0)->nullable(false)->comment("user_enroll 关联关系");
            $table->integer("delete_time")->default(0)->nullable(false)->comment("删除时间");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("user_pay_log",function (Blueprint $table){
            $table->dropColumn(['enroll_id',"delete_time"]);
        });
    }
}
