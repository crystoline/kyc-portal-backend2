<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_information', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('verification_id');

            $table->string('email', 20)->nullable();

            $table->string('phone_number', 20)->nullable();
            $table->string('phone_number2', 20)->nullable();



            $table->string('imei', 50)->nullable();
            $table->string('bvn', 50)->nullable();
            $table->string('bank_account_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('product_of_interest', 200)->nullable()->comment('Money Transfer/Withdrawal Bill Payment Gaming ');

            $table->string('designation')->nullable()->comment('Principal Agent, Sole Agent');
            $table->string('occupation')->nullable()->comment('Profession/Occupation');

            $table->text('home_address')->nullable();
            $table->text('outlet_address')->nullable();
            $table->string('outlet_type', 200)->nullable()->comment('Shop,Office,Kiosk,Umbrella,Mobile,Others ');
            $table->string('landmark', 200)->nullable();
            $table->unsignedBigInteger('lga_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->string('latitude', 100)->nullable();
            $table->string('name_of_acquirer', 200)->nullable()->comment('Name of Acquirer/TP');

            $table->unsignedTinyInteger('android_phone')->default('0')->comment('0=No, 1=Yes');
            $table->unsignedTinyInteger('bluetooth_printer')->default('0')->comment('0=No, 1=Yes');
            $table->text('signature')->nullable();


            $table->timestamps();
            $table->foreign('verification_id')->references('id')->on('verifications');
            $table->foreign('lga_id')->references('id')->on('lgas');
            $table->foreign('state_id')->references('id')->on('states');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personal_information');
    }
}
