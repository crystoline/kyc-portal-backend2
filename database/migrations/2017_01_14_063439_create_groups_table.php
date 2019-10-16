<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');
            $table->string('name',40)->unique();
            $table->string('role',40)->unique()->comment('URL friendly Group slug');
            $table->enum('enabled',['0','1','2','3'])->default('1')->comment('1=Enabled, 0=Disabled, 2=Pending Disable, 3=Pending Enabled');
            $table->timestamps();            $table->softDeletes();

            //$table->foreign('active_hour_id')->references('id')->on('active_hours');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('groups');
    }
}
