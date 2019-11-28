<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_agent_id')->nullable();
            $table->unsignedBigInteger('agent_type_id')->nullable();
            $table->unsignedBigInteger('territory_id')->nullable();
            $table->unsignedBigInteger('device_owner_id')->nullable();

            $table->string('code', 30)->unique();
            $table->string('type', 50)->default('agent')->comment('principal-agent, sole-agent');
            $table->unsignedTinyInteger('is_app_only')->default(0)->comment('0=No,1=Yes');
            $table->string('first_name', 180);
            $table->string('last_name', 180);
            $table->string('user_name', 180);
            $table->string('gender', 20)->comment('male, female');
            $table->date('date_of_birth');
            $table->string('email', 255)->nullable();
            $table->string('phone_number', 255)->nullable();
            $table->string('passport', 255)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('city', 200)->nullable();
            $table->unsignedBigInteger('lga_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();


            $table->date('last_verification_date')->nullable();
            $table->unsignedTinyInteger('status')->default('2')->comment('2=Pending, 1=Verified, 0=Re-Verification');



            // $table->foreign('device_owner_id')->references('id')->on('device_owners');
             $table->foreign('territory_id')->references('id')->on('territories');
             $table->foreign('lga_id')->references('id')->on('lgas');
            $table->foreign('state_id')->references('id')->on('states');

            $table->foreign('parent_agent_id')->references('id')->on('agents');
            $table->foreign('user_id')->references('id')->on('user_ids');
            $table->foreign('agent_type_id')->references('id')->on('agent_types');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agents');
    }
}
