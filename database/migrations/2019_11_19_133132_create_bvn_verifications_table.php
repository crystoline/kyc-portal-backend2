<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBvnVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bvn_verifications', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('data');
            $table->unsignedBigInteger('agent_id');
            $table->string('bvn', 11);
            $table->integer('status')->default('2');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents');
            $table->foreign('user_id')->references('id')->on('users');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bvn_verifications');
    }
}
