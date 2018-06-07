<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EnrollModifyDeleteTimeType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_enroll', function (Blueprint $table) {
            //delete_time默认值为null
            $table->integer('delete_time')->default(null)->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_enroll', function (Blueprint $table) {
            $table->integer('delete_time')->default(0)->nullable(false)->change();
        });
    }
}
