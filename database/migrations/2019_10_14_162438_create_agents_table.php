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
            $table->string('code', 30)->unique();
            $table->string('type', 50)->default('agent')->comment('principal-agent, sole-agent');
            $table->unsignedTinyInteger('is_app_only')->default(0)->comment('0=No,1=Yes');
            $table->string('first_name', 180);
            $table->string('last_name', 180);
            $table->string('user_name', 180);
            $table->string('gender', 20)->comment('male, female');
            $table->date('date_of_birth');
            $table->string('passport', 255)->nullable();
            $table->date('last_verification_date')->nullable();
            $table->unsignedTinyInteger('status')->default('2')->comment('2=Pending, 1=Verified, 0=Re-Verification');

            $table->foreign('parent_agent_id')->references('id')->on('agents');

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
