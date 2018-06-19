<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyRebateActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rebate_activity', function (Blueprint $table) {
            //关联的课程套餐表(course_package)ID
            $table->integer("package_id")->comment('关联的课程套餐表(course_package)ID');
            //修改字段price的名字
            $table->decimal('price_max', 8, 2)->comment('优惠活动最高价格')->default(0);
            //新增字段create_uid，创建人ID
            $table->integer("create_uid")->comment('创建人ID')->default(0);
            //新增字段update_uid，最后修改人ID
            $table->integer("update_uid")->comment('最后修改人ID')->default(0);
            //新增字段course_give,赠送课程列表json格式
            $table->text('course_give')->comment('赠送课程列表json格式');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rebate_activity', function (Blueprint $table) {
            $table->dropColumn(['price_max','create_uid','update_uid','course_give','package_id']);
        });
    }
}
