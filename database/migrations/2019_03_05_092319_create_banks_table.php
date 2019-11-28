<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('bank_type_id')->unsigned();
             $table->string('name', 180);
             $table->string('nibss_code', 100)->nullable();
             $table->string('isw_code', 100)->nullable();
             $table->integer('status')->unsigned()->default(1);
            $table->timestamps();
            $table->foreign('bank_type_id')->references('id')->on('bank_types');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banks');
    }
}
