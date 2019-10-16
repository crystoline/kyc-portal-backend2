<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGaurantorsInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gaurantors_information', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('full_name', 200)->nullable();
            $table->string('occupation', 200)->nullable()->comment('Profession/Occupation');
            $table->string('business_name', 200)->nullable()->comment('Business/Office Name');
            $table->text('office_address')->nullable();
            $table->string('position', 200)->nullable()->comment('Position/Status');
            $table->string('department', 200)->nullable()->comment('Deparment or Unit');
            $table->string('telephone_number', 30)->nullable();
            $table->string('email', 200)->nullable();
            $table->text('residential_address')->nullable();
            $table->string('mobile_number', 30)->nullable();
            $table->string('relationship', 30)->nullable()->comment('Relationship to the Applicant:');
            $table->unsignedSmallInteger('no_of_relations_ship_years')->nullable()->comment('Number of Years you have known the applicant?');
            $table->text('signature')->nullable();

            $table->text('witness_signature')->nullable();
            $table->string('witness_full_name', 200)->nullable();
            $table->string('witness_occupation', 200)->nullable()->comment('Profession/Occupation');
            $table->text('witness__address')->nullable();
            $table->string('witness_telephone_number', 30)->nullable();
            $table->string('witness_email', 200)->nullable();

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
        Schema::dropIfExists('gaurantors_information');
    }
}
