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
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('verification_period_id');
            $table->unsignedBigInteger('device_owner_id');
            $table->unsignedBigInteger('territory_id')->nullable();
            $table->unsignedBigInteger('parent_agent_id')->nullable();
            $table->unsignedBigInteger('agent_type_id')->nullable();

            /* Agent Information */
            $table->string('type', 50)->nullable()->default('agent')->comment('principal-agent, sole-agent');
            $table->unsignedTinyInteger('is_app_only')->nullable()->default(0)->comment('0=No,1=Yes');
            $table->string('first_name', 180)->nullable();
            $table->string('last_name', 180)->nullable();
            $table->string('user_name', 180)->nullable();
            $table->string('gender', 20)->nullable()->comment('male, female');
            $table->date('date_of_birth')->nullable();
            $table->string('passport', 255)->nullable();
            /* */
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('date')->nullable();
            $table->unsignedTinyInteger('status')->default('2')->comment('2=Pending, 1=Approved, 0=Declined,3=discarded, 9=Awaiting Approval');
            $table->timestamps();

            $table->foreign('territory_id')->references('id')->on('territories');
            //$table->foreign('device_owner_id')->references('id')->on('device_owners');

            $table->foreign('verification_period_id')->references('id')->on('verification_periods');
            $table->foreign('agent_id')->references('id')->on('agents');
            $table->foreign('parent_agent_id')->references('id')->on('agents');
            $table->foreign('agent_type_id')->references('id')->on('agent_types');
            $table->foreign('verified_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
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
