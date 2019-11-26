<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeValuesContactInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_values_contact_information', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('custom_attribute_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('mobile_phone');

            $table->foreign('custom_attribute_id')->references('id')->on('custom_attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute_values_contact_information');
    }
}
