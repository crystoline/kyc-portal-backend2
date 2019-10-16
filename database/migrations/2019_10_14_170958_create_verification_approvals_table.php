<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verification_approvals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('verification_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('status')->comment('3=discarded, 1=Approved, 0=Declined');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('verification_id')->references('id')->on('verifications');
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
        Schema::dropIfExists('verification_approvals');
    }
}
