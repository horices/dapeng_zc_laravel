<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOperatorToRosterCourseLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roster_course_log', function (Blueprint $table) {
            $table->string("operator_id","20")->default('')->comment("操作人ID");
            $table->string("operator_name","255")->default('')->comment("操作人姓名");
            $table->string("operator_ip","15")->default('')->comment("操作人姓名");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roster_course_log', function (Blueprint $table) {
            //
            $table->dropColumn(["operator_id","operator_name","operator_ip"]);
        });
    }
}
