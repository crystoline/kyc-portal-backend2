<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');
            $table->bigInteger('module_id')->unsigned()->nullable();
            $table->bigInteger('parent_task_id')->unsigned()->default(0)->comment('Parent task if any default is 0 meaning no parent');;
            $table->string('route',100)->unique()->comment('Matching laravel route name for this task');
            $table->string('name',100);
            $table->enum('task_type',['0','1','2','3'])->default('0')->comment('0=Menu, 1=Action, 2=Plugin Menu, 3=Plugin Action');
            $table->string('description',100);
            $table->enum('visibility',['0','1'])->default('1')->comment('Can this task be seen or not');
            $table->integer('order')->length(3);
            $table->string('icon')->length(20)->default('')->nullable();
            $table->string('extra')->length(100)->default('')->nullable();

            $table->unique(['name','task_type','parent_task_id','module_id']);
            $table->foreign('module_id')->references('id')->on('modules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tasks');
    }
}
