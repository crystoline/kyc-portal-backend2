<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelephoneVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telephone_verifications', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('agent_id');
            $table->string('telephone', 50)->unique();
            $table->string('code', 10);
            $table->tinyInteger('status')->length(1)->default(2)->comment('1=verified, 2=pending');
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telephone_verifications');
    }
}
