<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLgasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lgas', function (Blueprint $table) {
            $table->bigIncrements('id');

	        $table->bigInteger('state_id')->unsigned();
	        $table->string('name', 150)->nullable()/*->unique()*/;
	        $table->string('code', 4)->nullable()/*->unique()*/;
	        $table->timestamps();

	        $table->foreign('state_id')->references('id')->on('states')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lgas');
    }
}
