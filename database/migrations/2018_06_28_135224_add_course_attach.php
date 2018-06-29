<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCourseAttach extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_package', function (Blueprint $table) {
            $table->string("school_id",10)->comment("学院标识ID");
            $table->text("course_attach")->comment("附加课程和赠送课程json包");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_package', function (Blueprint $table) {
            $table->dropColumn([
                'school_id','course_attach'
            ]);
        });
    }
}
