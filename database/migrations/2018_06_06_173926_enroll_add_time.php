<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EnrollAddTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_enroll', function (Blueprint $table) {
            $table->integer('create_time')->default(0)->nullable(false);
            $table->integer('update_time')->default(0)->nullable(false);
            $table->integer('delete_time')->default(0)->nullable(false);
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
            $table->dropColumn([
                'create_time','update_time','delete_time'
            ]);
        });
    }
}
