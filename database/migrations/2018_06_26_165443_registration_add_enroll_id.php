<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RegistrationAddEnrollId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_registration', function (Blueprint $table) {
            $table->string('school_id', 10)->comment("学院标识ID");
            $table->string('wx', 30)->comment("学员微信号");
            $table->decimal('package_price', 8,2)->default(0)->comment("套餐的价格");
            $table->decimal('course_attach_all_price', 8,2)->default(0)->comment("附加课程总金额");
            $table->integer("enroll_id")->default(0)->comment("关联user_enroll表");
            $table->text("package_attach_content")->comment("附加套餐的信息json格式");
            //$table->decimal("package_total_price",8,2)->default(0)->comment("应交总金额");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_registration', function (Blueprint $table) {
            $table->dropColumn(["school_id","wx","package_price","course_attach_all_price","enroll_id","package_attach_content","package_total_price"]);
        });
    }
}
