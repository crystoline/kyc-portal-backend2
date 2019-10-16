<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('is_first_registration')->default(0)->comment('0=No,1=Yes');
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('personal_information_id')->nullable();
            $table->unsignedBigInteger('gaurantors_information_id')->nullable();
            $table->date('date')->nullable();
            $table->unsignedTinyInteger('status')->default('2')->comment('2=Pending, 1=Approved, 0=Declined,3=discarded');

            $table->timestamps();


            $table->foreign('agent_id')->references('id')->on('agents');
            $table->foreign('verified_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->foreign('personal_information_id')->references('id')->on('personal_informations');
            $table->foreign('gaurantors_information_id')->references('id')->on('gaurantors_informations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verifications');
    }
}
